<?php

// Realistic Human Hand Clap & Crowd Applause WAV Synthesizer Generator
$sampleRate = 44100;
$duration = 4.5; // seconds
$numSamples = (int)($sampleRate * $duration);
$samples = array_fill(0, $numSamples, 0.0);

// Generate 400 distinct human hand claps spread randomly over time
// simulating 40 people clapping enthusiastically in a hall
$numClaps = 450;

for ($c = 0; $c < $numClaps; $c++) {
    // Time distribution: dense burst at start (0 to 1.5s), sustaining then tapering
    $time = pow(mt_rand() / mt_getrandmax(), 1.15) * $duration;
    $startIdx = (int)($time * $sampleRate);
    
    // Each person has slightly different palm size (resonant frequency)
    $palmResonanceFreq = mt_rand(550, 1100); // Helmholtz cavity pop
    $skinSlapFreq = mt_rand(1800, 3400);     // High frequency skin slap
    $clapLen = mt_rand((int)($sampleRate * 0.015), (int)($sampleRate * 0.035));
    $amplitude = (0.2 + (mt_rand() / mt_getrandmax()) * 0.8) * (1.0 - ($time / $duration) * 0.7);

    for ($i = 0; $i < $clapLen && ($startIdx + $i) < $numSamples; $i++) {
        $t = $i / $sampleRate;
        $decay = exp(-$t * 180); // Sharp hand clap envelope
        
        // Helmholtz cavity pop (sine wave + noise)
        $pop = sin(2 * M_PI * $palmResonanceFreq * $t) * 0.4;
        
        // High skin slap (filtered noise simulation)
        $slap = ((mt_rand() / mt_getrandmax()) * 2.0 - 1.0) * sin(2 * M_PI * $skinSlapFreq * $t) * 0.6;
        
        // Combined hand clap wave
        $val = ($pop + $slap) * $decay * $amplitude;
        
        $samples[$startIdx + $i] += $val;
        
        // Hall Reverb Reflections (Room acoustics)
        $reverbDelays = [0.012, 0.028, 0.055, 0.090];
        $reverbGains  = [0.35,  0.22,  0.15,  0.08];
        
        foreach ($reverbDelays as $rdIdx => $rd) {
            $rIdx = $startIdx + $i + (int)($rd * $sampleRate);
            if ($rIdx < $numSamples) {
                $samples[$rIdx] += $val * $reverbGains[$rdIdx];
            }
        }
    }
}

// Background crowd cheer vocal swell (hum / cheer resonance)
for ($i = 0; $i < $numSamples; $i++) {
    $t = $i / $sampleRate;
    $swellEnv = sin(M_PI * min(1.0, $t / 4.0));
    $cheerNoise = ((mt_rand() / mt_getrandmax()) * 2.0 - 1.0);
    $vocalResonance = sin(2 * M_PI * 450 * $t) * 0.3 + sin(2 * M_PI * 750 * $t) * 0.2;
    $samples[$i] += $cheerNoise * $vocalResonance * $swellEnv * 0.08;
}

// Normalize samples to prevent clipping
$maxVal = 0.0001;
for ($i = 0; $i < $numSamples; $i++) {
    if (abs($samples[$i]) > $maxVal) {
        $maxVal = abs($samples[$i]);
    }
}
$scale = 0.90 / $maxVal;

// Convert to 16-bit PCM WAV format data
$pcmData = '';
for ($i = 0; $i < $numSamples; $i++) {
    $s = max(-1.0, min(1.0, $samples[$i] * $scale));
    $val = (int)($s * 32767);
    $pcmData .= pack('v', $val);
}

// Build WAV Header
$dataSize = strlen($pcmData);
$header = 'RIFF' . pack('V', $dataSize + 36) . 'WAVEfmt ' . pack('V', 16) . pack('v', 1) . pack('v', 1) . pack('V', $sampleRate) . pack('V', $sampleRate * 2) . pack('v', 2) . pack('v', 16) . 'data' . pack('V', $dataSize);

file_put_contents('a:/wamp64/www/lims/public/frontAssets/applause.wav', $header . $pcmData);
echo "Generated WAV size: " . filesize('a:/wamp64/www/lims/public/frontAssets/applause.wav') . " bytes\n";
