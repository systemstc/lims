<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update find_similar_names to search m07_customers by name and GST with fuzzy logic
        DB::unprepared("DROP PROCEDURE IF EXISTS `find_similar_names`");
        DB::unprepared("
            CREATE PROCEDURE `find_similar_names`(IN input_name TEXT)
            BEGIN
                DECLARE p_normalized_input TEXT;
                DECLARE p_trimmed_input VARCHAR(255);
                DECLARE p_lower_input VARCHAR(255);
                DECLARE p_fuzzy_threshold DECIMAL(10,2);
                
                SET p_trimmed_input = TRIM(input_name);
                SET p_lower_input = LOWER(p_trimmed_input COLLATE utf8mb4_unicode_ci);
                SET p_normalized_input = normalize_name(p_trimmed_input COLLATE utf8mb4_unicode_ci);
                SET p_fuzzy_threshold = CHAR_LENGTH(p_normalized_input COLLATE utf8mb4_unicode_ci) * 0.6;
                
                -- Temp table for all customers with normalized names and GST
                CREATE TEMPORARY TABLE IF NOT EXISTS temp_customer_data (
                    id INT,
                    ro_id INT,
                    name VARCHAR(255),
                    gst VARCHAR(255),
                    lower_name TEXT,
                    normalized_db_name TEXT,
                    lower_gst VARCHAR(255),
                    json_words JSON
                );
                TRUNCATE TABLE temp_customer_data;
                
                INSERT INTO temp_customer_data
                SELECT
                    m07_customer_id,
                    m04_ro_id,
                    m07_name,
                    COALESCE(m07_gst, ''),
                    LOWER(COALESCE(m07_name, '') COLLATE utf8mb4_unicode_ci),
                    normalize_name(COALESCE(m07_name, '') COLLATE utf8mb4_unicode_ci),
                    LOWER(TRIM(COALESCE(m07_gst, '')) COLLATE utf8mb4_unicode_ci),
                    CONCAT('[\"', REPLACE(REPLACE(LOWER(COALESCE(m07_name, '')), '\"', ''), ' ', '\",\x22'), '\"]')
                FROM m07_customers
                WHERE m07_name IS NOT NULL AND TRIM(m07_name) != '';
                
                -- Exact matches (either exact Name or exact GST)
                DROP TEMPORARY TABLE IF EXISTS temp_exact_matches;
                CREATE TEMPORARY TABLE temp_exact_matches AS
                SELECT
                    id,
                    ro_id,
                    name,
                    gst,
                    'exact' AS match_type,
                    0 AS levenshtein_distance,
                    NULL AS fuzzy_threshold
                FROM temp_customer_data
                WHERE lower_name = p_lower_input
                   OR (lower_gst != '' AND lower_gst = p_lower_input);
                   
                -- Normalized matches
                DROP TEMPORARY TABLE IF EXISTS temp_normalized_matches;
                CREATE TEMPORARY TABLE temp_normalized_matches AS
                SELECT
                    id,
                    ro_id,
                    name,
                    gst,
                    'normalized' AS match_type,
                    0 AS levenshtein_distance,
                    NULL AS fuzzy_threshold
                FROM temp_customer_data
                WHERE id NOT IN (SELECT id FROM temp_exact_matches)
                  AND (
                      normalized_db_name = p_normalized_input COLLATE utf8mb4_unicode_ci
                      OR (lower_gst != '' AND REPLACE(lower_gst, ' ', '') = REPLACE(p_lower_input, ' ', ''))
                  );
                  
                -- Substring matches (in Name or GST)
                DROP TEMPORARY TABLE IF EXISTS temp_substring_matches;
                CREATE TEMPORARY TABLE temp_substring_matches AS
                SELECT
                    id,
                    ro_id,
                    name,
                    gst,
                    'substring' AS match_type,
                    NULL AS levenshtein_distance,
                    NULL AS fuzzy_threshold
                FROM temp_customer_data
                WHERE id NOT IN (SELECT id FROM temp_exact_matches)
                  AND id NOT IN (SELECT id FROM temp_normalized_matches)
                  AND (
                      lower_name LIKE CONCAT('%', p_lower_input, '%')
                      OR (lower_gst != '' AND lower_gst LIKE CONCAT('%', p_lower_input, '%'))
                  );
                  
                -- Fuzzy matches
                DROP TEMPORARY TABLE IF EXISTS temp_fuzzy_matches;
                CREATE TEMPORARY TABLE temp_fuzzy_matches AS
                SELECT
                    t.id,
                    t.ro_id,
                    t.name,
                    t.gst,
                    'fuzzy' AS match_type,
                    LEAST(
                        levenshtein(t.normalized_db_name, p_normalized_input COLLATE utf8mb4_unicode_ci),
                        COALESCE(
                            (
                                SELECT MIN(levenshtein(w, p_lower_input))
                                FROM JSON_TABLE(
                                    t.json_words,
                                    '$[*]' COLUMNS (w VARCHAR(255) PATH '$')
                                ) AS jt
                                WHERE CHAR_LENGTH(w) >= 3
                            ),
                            999
                        ),
                        IF(t.lower_gst != '' AND CHAR_LENGTH(p_lower_input) >= 5,
                           levenshtein(t.lower_gst, p_lower_input),
                           999
                        )
                    ) AS levenshtein_distance,
                    p_fuzzy_threshold AS fuzzy_threshold
                FROM temp_customer_data t
                WHERE t.id NOT IN (SELECT id FROM temp_exact_matches)
                  AND t.id NOT IN (SELECT id FROM temp_normalized_matches)
                  AND t.id NOT IN (SELECT id FROM temp_substring_matches)
                  AND (
                      levenshtein(t.normalized_db_name, p_normalized_input COLLATE utf8mb4_unicode_ci) <= p_fuzzy_threshold
                      OR (
                          CHAR_LENGTH(p_lower_input) >= 3
                          AND EXISTS (
                              SELECT 1
                              FROM JSON_TABLE(
                                  t.json_words,
                                  '$[*]' COLUMNS (w VARCHAR(255) PATH '$')
                              ) AS jt2
                              WHERE CHAR_LENGTH(w) >= 3
                                AND levenshtein(w, p_lower_input) <= IF(CHAR_LENGTH(p_lower_input) <= 4, 1, 2)
                          )
                      )
                      OR (
                          CHAR_LENGTH(p_lower_input) >= 5
                          AND t.lower_gst != ''
                          AND levenshtein(t.lower_gst, p_lower_input) <= 3
                      )
                  );
                  
                -- Final union with all results
                SELECT * FROM temp_exact_matches
                UNION ALL
                SELECT * FROM temp_normalized_matches
                UNION ALL
                SELECT * FROM temp_substring_matches
                UNION ALL
                SELECT * FROM temp_fuzzy_matches
                ORDER BY 
                    CASE match_type
                        WHEN 'exact' THEN 1
                        WHEN 'normalized' THEN 2
                        WHEN 'substring' THEN 3
                        WHEN 'fuzzy' THEN 4
                        ELSE 5
                    END,
                    COALESCE(levenshtein_distance, 0) ASC,
                    name ASC;
                    
                -- Clean up temp tables
                DROP TEMPORARY TABLE IF EXISTS temp_customer_data;
                DROP TEMPORARY TABLE IF EXISTS temp_exact_matches;
                DROP TEMPORARY TABLE IF EXISTS temp_normalized_matches;
                DROP TEMPORARY TABLE IF EXISTS temp_substring_matches;
                DROP TEMPORARY TABLE IF EXISTS temp_fuzzy_matches;
            END;
        ");

        // 2. Update find_similar_name as well
        DB::unprepared("DROP PROCEDURE IF EXISTS `find_similar_name`");
        DB::unprepared("
            CREATE PROCEDURE `find_similar_name`(IN input_name TEXT)
            BEGIN
                DECLARE normalized_input TEXT;
                DECLARE lower_input VARCHAR(255);
                SET lower_input = LOWER(TRIM(input_name));
                SET normalized_input = normalize_n(input_name);
                
                SELECT m07_customer_id AS id, m04_ro_id AS ro_id, m07_name AS name, m07_gst AS gst, 'exact' AS match_type
                FROM m07_customers
                WHERE LOWER(m07_name) = lower_input OR (m07_gst IS NOT NULL AND LOWER(m07_gst) = lower_input)
                UNION
                SELECT m07_customer_id AS id, m04_ro_id AS ro_id, m07_name AS name, m07_gst AS gst, 'normalized' AS match_type
                FROM m07_customers
                WHERE (normalize_n(m07_name) = normalized_input)
                  AND LOWER(m07_name) != lower_input
                UNION
                SELECT m07_customer_id AS id, m04_ro_id AS ro_id, m07_name AS name, m07_gst AS gst, 'substring' AS match_type
                FROM m07_customers
                WHERE (LOWER(m07_name) LIKE CONCAT('%', lower_input, '%')
                   OR (m07_gst IS NOT NULL AND LOWER(m07_gst) LIKE CONCAT('%', lower_input, '%')))
                  AND LOWER(m07_name) != lower_input
                  AND normalize_n(m07_name) != normalized_input
                UNION
                SELECT m07_customer_id AS id, m04_ro_id AS ro_id, m07_name AS name, m07_gst AS gst, 'fuzzy' AS match_type
                FROM m07_customers
                WHERE 
                  ((1 - levenshtein(normalize_n(m07_name), normalized_input) / 
                      GREATEST(CHAR_LENGTH(normalize_n(m07_name)), CHAR_LENGTH(normalized_input))
                  ) * 100 >= 80
                  AND normalize_n(m07_name) != normalized_input)
                  OR (m07_gst IS NOT NULL AND m07_gst != '' AND CHAR_LENGTH(lower_input) >= 5 AND levenshtein(LOWER(m07_gst), lower_input) <= 3);
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to legacy customers table if needed
        DB::unprepared("DROP PROCEDURE IF EXISTS `find_similar_name`");
        DB::unprepared("
            CREATE PROCEDURE `find_similar_name`(IN input_name TEXT)
            BEGIN
              DECLARE normalized_input TEXT;
              SET normalized_input = normalize_n(input_name);
              SELECT id, name, 'exact' AS match_type
              FROM customers
              WHERE LOWER(name) = LOWER(input_name)
              UNION
              SELECT id, name, 'normalized' AS match_type
              FROM customers
              WHERE normalize_n(name) = normalized_input
                AND LOWER(name) != LOWER(input_name)
              UNION
              SELECT id, name, 'fuzzy' AS match_type
              FROM customers
              WHERE 
                (1 - levenshtein(normalize_n(name), normalized_input) / 
                    GREATEST(CHAR_LENGTH(normalize_n(name)), CHAR_LENGTH(normalized_input))
                ) * 100 >= 80
                AND normalize_n(name) != normalized_input;
            END;
        ");
    }
};
