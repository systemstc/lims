@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mt-2">MANUSCRIPT / DATASHEET</h5>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-primary fs-6 px-3 py-2">
                                        Test Report No: {{ $manuscript->test_report_no ?? 'New Document' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ isset($manuscript) ? route('template_manuscript', $manuscript->id) : route('template_manuscript') }}" 
                              method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($manuscript))
                                @method('PUT')
                            @endif

                            <!-- Header Information Table -->
                            <div class="card card-bordered mb-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="bg-light fw-bold" width="20%">Test Report No:</td>
                                                <td width="30%">
                                                    <input type="text" class="form-control border-0" name="test_report_no" 
                                                           value="{{ old('test_report_no', $manuscript->test_report_no ?? '') }}">
                                                </td>
                                                <td class="bg-light fw-bold" width="20%">Date:</td>
                                                <td width="30%">
                                                    <input type="date" class="form-control border-0" name="test_date" 
                                                           value="{{ old('test_date', $manuscript->test_date ?? date('Y-m-d')) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light fw-bold">No of samples:</td>
                                                <td>
                                                    <input type="number" class="form-control border-0" name="no_of_samples" 
                                                           value="{{ old('no_of_samples', $manuscript->no_of_samples ?? '1') }}">
                                                </td>
                                                <td class="bg-light fw-bold">Sample Characteristics:</td>
                                                <td>
                                                    <input type="text" class="form-control border-0" name="sample_characteristics" 
                                                           value="{{ old('sample_characteristics', $manuscript->sample_characteristics ?? 'Fabric') }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light fw-bold">Date of Performance of Tests:</td>
                                                <td>
                                                    <input type="date" class="form-control border-0" name="performance_date" 
                                                           value="{{ old('performance_date', $manuscript->performance_date ?? '') }}">
                                                </td>
                                                <td class="bg-light fw-bold">Date of allotment of sample:</td>
                                                <td>
                                                    <input type="text" class="form-control border-0" name="allotment_ref" 
                                                           value="{{ old('allotment_ref', $manuscript->allotment_ref ?? '04/24(Q)/02') }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light fw-bold">QAO/JQAO/Analyst:</td>
                                                <td>
                                                    <input type="text" class="form-control border-0" name="qao_analyst" 
                                                           value="{{ old('qao_analyst', $manuscript->qao_analyst ?? '') }}">
                                                </td>
                                                <td class="bg-light fw-bold">Technical Manager:</td>
                                                <td>
                                                    <input type="text" class="form-control border-0" name="technical_manager" 
                                                           value="{{ old('technical_manager', $manuscript->technical_manager ?? '') }}">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Test Results Table -->
                            <div class="card card-bordered mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0 text-white">T E S T &nbsp;&nbsp; R E S U L T S</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="8%">Sample Mark</th>
                                                <th colspan="2">Laboratory Sample No: {{ $manuscript->lab_sample_no ?? '0253032526-5522' }}</th>
                                            </tr>
                                            <tr>
                                                <th width="8%">S.No</th>
                                                <th width="60%">Test Name</th>
                                                <th width="32%">Result/Observation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Test 1 -->
                                            <tr>
                                                <td class="text-center fw-bold">1</td>
                                                <td>Whether Coated or Otherwise, If Coated, Nature of Coating, Whether Coating is Visible with Naked Eye or Not (.)</td>
                                                <td>
                                                    <textarea class="form-control border-0" name="test_1_coating" rows="2">{{ old('test_1_coating', $manuscript->test_1_coating ?? '') }}</textarea>
                                                </td>
                                            </tr>

                                            <!-- Test 2 -->
                                            <tr>
                                                <td class="text-center fw-bold">2</td>
                                                <td>Whether Embroidery fabric with Visible ground or not (.)</td>
                                                <td>
                                                    <select class="form-select border-0" name="test_2_embroidery">
                                                        <option value="">Select...</option>
                                                        <option value="Yes" {{ old('test_2_embroidery', $manuscript->test_2_embroidery ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                        <option value="No" {{ old('test_2_embroidery', $manuscript->test_2_embroidery ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <!-- Test 3 -->
                                            <tr>
                                                <td class="text-center fw-bold">3</td>
                                                <td>Whether Pile (.)</td>
                                                <td>
                                                    <select class="form-select border-0" name="test_3_pile">
                                                        <option value="">Select...</option>
                                                        <option value="Yes" {{ old('test_3_pile', $manuscript->test_3_pile ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                        <option value="No" {{ old('test_3_pile', $manuscript->test_3_pile ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <!-- Test 4 -->
                                            <tr>
                                                <td class="text-center fw-bold">4</td>
                                                <td>Whether PVC/PU /Others/Embossed/Plain or Printed (.)</td>
                                                <td>
                                                    <select class="form-select border-0" name="test_4_pvc_pu">
                                                        <option value="">Select...</option>
                                                        <option value="PVC" {{ old('test_4_pvc_pu', $manuscript->test_4_pvc_pu ?? '') == 'PVC' ? 'selected' : '' }}>PVC</option>
                                                        <option value="PU" {{ old('test_4_pvc_pu', $manuscript->test_4_pvc_pu ?? '') == 'PU' ? 'selected' : '' }}>PU</option>
                                                        <option value="Others" {{ old('test_4_pvc_pu', $manuscript->test_4_pvc_pu ?? '') == 'Others' ? 'selected' : '' }}>Others</option>
                                                        <option value="Embossed" {{ old('test_4_pvc_pu', $manuscript->test_4_pvc_pu ?? '') == 'Embossed' ? 'selected' : '' }}>Embossed</option>
                                                        <option value="Plain" {{ old('test_4_pvc_pu', $manuscript->test_4_pvc_pu ?? '') == 'Plain' ? 'selected' : '' }}>Plain</option>
                                                        <option value="Printed" {{ old('test_4_pvc_pu', $manuscript->test_4_pvc_pu ?? '') == 'Printed' ? 'selected' : '' }}>Printed</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <!-- Test 5 - Fibre Identification -->
                                            <tr>
                                                <td class="text-center fw-bold align-top">5</td>
                                                <td>
                                                    <strong>Identification of Fibre (IS:667:1981)</strong><br>
                                                    <div class="mt-2">
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <label class="form-label small mb-1">L.S.</label>
                                                                <textarea class="form-control form-control-sm" name="test_5_ls" rows="2">{{ old('test_5_ls', $manuscript->test_5_ls ?? '') }}</textarea>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label small mb-1">C.S.</label>
                                                                <textarea class="form-control form-control-sm" name="test_5_cs" rows="2">{{ old('test_5_cs', $manuscript->test_5_cs ?? '') }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="row g-2 mt-1">
                                                            <div class="col-6">
                                                                <label class="form-label small mb-1">Burning Test</label>
                                                                <textarea class="form-control form-control-sm" name="test_5_burning" rows="2">{{ old('test_5_burning', $manuscript->test_5_burning ?? '') }}</textarea>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label small mb-1">Solubility Test</label>
                                                                <textarea class="form-control form-control-sm" name="test_5_solubility" rows="2">{{ old('test_5_solubility', $manuscript->test_5_solubility ?? '') }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <label class="form-label small mb-1">IS: 667: 1981 Result</label>
                                                            <input type="text" class="form-control form-control-sm" name="test_5_result" 
                                                                   value="{{ old('test_5_result', $manuscript->test_5_result ?? '') }}">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <textarea class="form-control border-0" name="test_5_final_result" rows="8" 
                                                              placeholder="Final identification result">{{ old('test_5_final_result', $manuscript->test_5_final_result ?? '') }}</textarea>
                                                </td>
                                            </tr>

                                            <!-- Test 6 - Aryl Amines -->
                                            <tr>
                                                <td class="text-center fw-bold align-top">6</td>
                                                <td>
                                                    <strong>Aryl amines (IS 15570:2005)</strong><br>
                                                    <div class="mt-2">
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-1">Weight of Sample</label>
                                                            <input type="text" class="form-control form-control-sm" name="test_6_weight" 
                                                                   value="{{ old('test_6_weight', $manuscript->test_6_weight ?? '') }}">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-1">Analysis using: HPTLC / HPLC / GCMS</label>
                                                            <select class="form-select form-control-sm" name="test_6_analysis_method">
                                                                <option value="">Select Method...</option>
                                                                <option value="HPTLC" {{ old('test_6_analysis_method', $manuscript->test_6_analysis_method ?? '') == 'HPTLC' ? 'selected' : '' }}>HPTLC</option>
                                                                <option value="HPLC" {{ old('test_6_analysis_method', $manuscript->test_6_analysis_method ?? '') == 'HPLC' ? 'selected' : '' }}>HPLC</option>
                                                                <option value="GCMS" {{ old('test_6_analysis_method', $manuscript->test_6_analysis_method ?? '') == 'GCMS' ? 'selected' : '' }}>GCMS</option>
                                                            </select>
                                                        </div>
                                                        <div class="small">
                                                            Rf /λ max/Colour/RT of the sample peak does /does not match with that of standard banned amines 1-22/24 listed in the test method
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mb-2">
                                                        <select class="form-select border-0" name="test_6_match_result">
                                                            <option value="">Select Result...</option>
                                                            <option value="does match" {{ old('test_6_match_result', $manuscript->test_6_match_result ?? '') == 'does match' ? 'selected' : '' }}>Does Match</option>
                                                            <option value="does not match" {{ old('test_6_match_result', $manuscript->test_6_match_result ?? '') == 'does not match' ? 'selected' : '' }}>Does Not Match</option>
                                                        </select>
                                                    </div>
                                                    <textarea class="form-control border-0" name="test_6_details" rows="4" 
                                                              placeholder="Rf /λ max/Colour/RT Details">{{ old('test_6_details', $manuscript->test_6_details ?? '') }}</textarea>
                                                </td>
                                            </tr>

                                            <!-- Test 7 - Fibre Blend Composition -->
                                            <tr>
                                                <td class="text-center fw-bold align-top">7</td>
                                                <td>
                                                    <strong>Fibre Blend Composition (%) (.)</strong><br>
                                                    <div class="small mt-1">(Based on clean dry mass with % addition for Moisture)</div>
                                                    <div class="mt-2">
                                                        <div class="row g-2">
                                                            <div class="col-3">
                                                                <label class="form-label small mb-1">Bone Dry Wt (S1):</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm" name="test_7_s1" 
                                                                       value="{{ old('test_7_s1', $manuscript->test_7_s1 ?? '') }}">
                                                            </div>
                                                            <div class="col-3">
                                                                <label class="form-label small mb-1">(S2):</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm" name="test_7_s2" 
                                                                       value="{{ old('test_7_s2', $manuscript->test_7_s2 ?? '') }}">
                                                            </div>
                                                            <div class="col-3">
                                                                <label class="form-label small mb-1">Bone Dry Wt (F1):</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm" name="test_7_f1" 
                                                                       value="{{ old('test_7_f1', $manuscript->test_7_f1 ?? '') }}">
                                                            </div>
                                                            <div class="col-3">
                                                                <label class="form-label small mb-1">(F2):</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm" name="test_7_f2" 
                                                                       value="{{ old('test_7_f2', $manuscript->test_7_f2 ?? '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <textarea class="form-control border-0" name="test_7_composition" rows="4" 
                                                              placeholder="Final composition %">{{ old('test_7_composition', $manuscript->test_7_composition ?? '') }}</textarea>
                                                </td>
                                            </tr>

                                            <!-- Test 8 - Thickness -->
                                            <tr>
                                                <td class="text-center fw-bold">8</td>
                                                <td>Thickness of Fabric (mm) (Using thickness gauge)</td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control border-0" name="test_8_thickness" 
                                                           value="{{ old('test_8_thickness', $manuscript->test_8_thickness ?? '') }}">
                                                </td>
                                            </tr>

                                            <!-- Test 9 - Weight of Sample -->
                                            <tr>
                                                <td class="text-center fw-bold align-top">9</td>
                                                <td>
                                                    <strong>Weight of Sample (TC/Lab TM-03)</strong><br>
                                                    <div class="mt-2">
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-1">Specimen Dimension (25 X 25 cm / 10 X 10 cm / Pattern)</label>
                                                            <select class="form-select form-control-sm" name="test_9_dimension">
                                                                <option value="">Select Dimension...</option>
                                                                <option value="25 X 25 cm" {{ old('test_9_dimension', $manuscript->test_9_dimension ?? '') == '25 X 25 cm' ? 'selected' : '' }}>25 X 25 cm</option>
                                                                <option value="10 X 10 cm" {{ old('test_9_dimension', $manuscript->test_9_dimension ?? '') == '10 X 10 cm' ? 'selected' : '' }}>10 X 10 cm</option>
                                                                <option value="Pattern" {{ old('test_9_dimension', $manuscript->test_9_dimension ?? '') == 'Pattern' ? 'selected' : '' }}>Pattern</option>
                                                            </select>
                                                        </div>
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <label class="form-label small mb-1">Weight (g) of S1</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm" name="test_9_weight_s1" 
                                                                       value="{{ old('test_9_weight_s1', $manuscript->test_9_weight_s1 ?? '') }}">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label small mb-1">S2</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm" name="test_9_weight_s2" 
                                                                       value="{{ old('test_9_weight_s2', $manuscript->test_9_weight_s2 ?? '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mb-2">
                                                        <label class="form-label small mb-1">Gram/sq.mtr</label>
                                                        <div class="row g-2">
                                                            <div class="col-4">
                                                                <label class="small">1.</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm border-0" name="test_9_gsm_1" 
                                                                       value="{{ old('test_9_gsm_1', $manuscript->test_9_gsm_1 ?? '') }}" readonly>
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="small">2.</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm border-0" name="test_9_gsm_2" 
                                                                       value="{{ old('test_9_gsm_2', $manuscript->test_9_gsm_2 ?? '') }}" readonly>
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="small">Avg.</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm border-0" name="test_9_gsm_avg" 
                                                                       value="{{ old('test_9_gsm_avg', $manuscript->test_9_gsm_avg ?? '') }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Additional Tests Table (Page 2) -->
                            <div class="card card-bordered mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0 text-white">A D D I T I O N A L &nbsp;&nbsp; T E S T S</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="8%">S.No</th>
                                                <th width="60%">Test Name</th>
                                                <th width="32%">Result/Observation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Test 10 -->
                                            <tr>
                                                <td class="text-center fw-bold align-top">10</td>
                                                <td>
                                                    <strong>Whether made of Staple spun yarn/Filament yarn /Staple Fiber (In house)</strong>
                                                    <div class="mt-2">
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <div class="small fw-bold">Warp</div>
                                                                <div class="mt-1">
                                                                    <label class="small">% of Staple Yarn</label>
                                                                    <input type="number" class="form-control form-control-sm" name="test_10_warp_staple" 
                                                                           value="{{ old('test_10_warp_staple', $manuscript->test_10_warp_staple ?? '') }}">
                                                                </div>
                                                                <div class="mt-1">
                                                                    <label class="small">% of Filament Yarn</label>
                                                                    <input type="number" class="form-control form-control-sm" name="test_10_warp_filament" 
                                                                           value="{{ old('test_10_warp_filament', $manuscript->test_10_warp_filament ?? '') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="small fw-bold">Weft</div>
                                                                <div class="mt-1">
                                                                    <label class="small">% of Staple Yarn</label>
                                                                    <input type="number" class="form-control form-control-sm" name="test_10_weft_staple" 
                                                                           value="{{ old('test_10_weft_staple', $manuscript->test_10_weft_staple ?? '') }}">
                                                                </div>
                                                                <div class="mt-1">
                                                                    <label class="small">% of Filament Yarn</label>
                                                                    <input type="number" class="form-control form-control-sm" name="test_10_weft_filament" 
                                                                           value="{{ old('test_10_weft_filament', $manuscript->test_10_weft_filament ?? '') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <textarea class="form-control border-0" name="test_10_result" rows="6" 
                                                              placeholder="Overall yarn composition result">{{ old('test_10_result', $manuscript->test_10_result ?? '') }}</textarea>
                                                </td>
                                            </tr>

                                            <!-- Test 11 -->
                                            <tr>
                                                <td class="text-center fw-bold align-top">11</td>
                                                <td>
                                                    <strong>Whether Texturised Yarn/ Non Texturised Yarn (In house)</strong>
                                                    <div class="mt-2">
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <div class="small fw-bold">Warp</div>
                                                                <div class="mt-1">
                                                                    <label class="small">% of Texturised yarn</label>
                                                                    <input type="number" class="form-control form-control-sm" name="test_11_warp_texturised" 
                                                                           value="{{ old('test_11_warp_texturised', $manuscript->test_11_warp_texturised ?? '') }}">
                                                                </div>
                                                                <div class="mt-1">
                                                                    <label class="small">% of Non Texturised yarn</label>
                                                                    <input type="number" class="form-control form-control-sm" name="test_11_warp_non_texturised" 
                                                                           value="{{ old('test_11_warp_non_texturised', $manuscript->test_11_warp_non_texturised ?? '') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="small fw-bold">Weft</div>
                                                                <div class="mt-1">
                                                                    <label class="small">% of Texturised yarn</label>
                                                                    <input type="number" class="form-control form-control-sm" name="test_11_weft_texturised" 
                                                                           value="{{ old('test_11_weft_texturised', $manuscript->test_11_weft_texturised ?? '') }}">
                                                                </div>
                                                                <div class="mt-1">
                                                                    <label class="small">% of Non Texturised yarn</label>
                                                                    <input type="number" class="form-control form-control-sm" name="test_11_weft_non_texturised" 
                                                                           value="{{ old('test_11_weft_non_texturised', $manuscript->test_11_weft_non_texturised ?? '') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <textarea class="form-control border-0" name="test_11_result" rows="6" 
                                                              placeholder="Texturization result">{{ old('test_11_result', $manuscript->test_11_result ?? '') }}</textarea>
                                                </td>
                                            </tr>

                                            <!-- Test 12 -->
                                            <tr>
                                                <td class="text-center fw-bold">12</td>
                                                <td>Whether Woven / Knitted / Non woven / Weaving (.)</td>
                                                <td>
                                                    <select class="form-select border-0" name="test_12_fabric_type">
                                                        <option value="">Select...</option>
                                                        <option value="Woven" {{ old('test_12_fabric_type', $manuscript->test_12_fabric_type ?? '') == 'Woven' ? 'selected' : '' }}>Woven</option>
                                                        <option value="Knitted" {{ old('test_12_fabric_type', $manuscript->test_12_fabric_type ?? '') == 'Knitted' ? 'selected' : '' }}>Knitted</option>
                                                        <option value="Non woven" {{ old('test_12_fabric_type', $manuscript->test_12_fabric_type ?? '') == 'Non woven' ? 'selected' : '' }}>Non woven</option>
                                                        <option value="Weaving" {{ old('test_12_fabric_type', $manuscript->test_12_fabric_type ?? '') == 'Weaving' ? 'selected' : '' }}>Weaving</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <!-- Test 13 -->
                                            <tr>
                                                <td class="text-center fw-bold">13</td>
                                                <td>Whether Unbleached/Bleached/Dyed/Printed/Yarns of Different Colour (In house)</td>
                                                <td>
                                                    <select class="form-select border-0" name="test_13_treatment">
                                                        <option value="">Select...</option>
                                                        <option value="Unbleached" {{ old('test_13_treatment', $manuscript->test_13_treatment ?? '') == 'Unbleached' ? 'selected' : '' }}>Unbleached</option>
                                                        <option value="Bleached" {{ old('test_13_treatment', $manuscript->test_13_treatment ?? '') == 'Bleached' ? 'selected' : '' }}>Bleached</option>
                                                        <option value="Dyed" {{ old('test_13_treatment', $manuscript->test_13_treatment ?? '') == 'Dyed' ? 'selected' : '' }}>Dyed</option>
                                                        <option value="Printed" {{ old('test_13_treatment', $manuscript->test_13_treatment ?? '') == 'Printed' ? 'selected' : '' }}>Printed</option>
                                                        <option value="Yarns of Different Colour" {{ old('test_13_treatment', $manuscript->test_13_treatment ?? '') == 'Yarns of Different Colour' ? 'selected' : '' }}>Yarns of Different Colour</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <!-- Test 14 -->
                                            <tr>
                                                <td class="text-center fw-bold">14</td>
                                                <td>Whether made of Cut pile /long pile/loop pile or other pile (In house)</td>
                                                <td>
                                                    <select class="form-select border-0" name="test_14_pile_type">
                                                        <option value="">Select...</option>
                                                        <option value="Cut pile" {{ old('test_14_pile_type', $manuscript->test_14_pile_type ?? '') == 'Cut pile' ? 'selected' : '' }}>Cut pile</option>
                                                        <option value="Long pile" {{ old('test_14_pile_type', $manuscript->test_14_pile_type ?? '') == 'Long pile' ? 'selected' : '' }}>Long pile</option>
                                                        <option value="Loop pile" {{ old('test_14_pile_type', $manuscript->test_14_pile_type ?? '') == 'Loop pile' ? 'selected' : '' }}>Loop pile</option>
                                                        <option value="Other pile" {{ old('test_14_pile_type', $manuscript->test_14_pile_type ?? '') == 'Other pile' ? 'selected' : '' }}>Other pile</option>
                                                        <option value="No pile" {{ old('test_14_pile_type', $manuscript->test_14_pile_type ?? '') == 'No pile' ? 'selected' : '' }}>No pile</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <!-- Test 15 -->
                                            <tr>
                                                <td class="text-center fw-bold">15</td>
                                                <td>Whether Coated/Laminated/Impregnated / Covered (.)</td>
                                                <td>
                                                    <select class="form-select border-0" name="test_15_coating_type">
                                                        <option value="">Select...</option>
                                                        <option value="Coated" {{ old('test_15_coating_type', $manuscript->test_15_coating_type ?? '') == 'Coated' ? 'selected' : '' }}>Coated</option>
                                                        <option value="Laminated" {{ old('test_15_coating_type', $manuscript->test_15_coating_type ?? '') == 'Laminated' ? 'selected' : '' }}>Laminated</option>
                                                        <option value="Impregnated" {{ old('test_15_coating_type', $manuscript->test_15_coating_type ?? '') == 'Impregnated' ? 'selected' : '' }}>Impregnated</option>
                                                        <option value="Covered" {{ old('test_15_coating_type', $manuscript->test_15_coating_type ?? '') == 'Covered' ? 'selected' : '' }}>Covered</option>
                                                        <option value="None" {{ old('test_15_coating_type', $manuscript->test_15_coating_type ?? '') == 'None' ? 'selected' : '' }}>None</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <!-- Test 16 -->
                                            <tr>
                                                <td class="text-center fw-bold">16</td>
                                                <td>Whether Embroidered or not? (..)</td>
                                                <td>
                                                    <select class="form-select border-0" name="test_16_embroidered">
                                                        <option value="">Select...</option>
                                                        <option value="Yes" {{ old('test_16_embroidered', $manuscript->test_16_embroidered ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                        <option value="No" {{ old('test_16_embroidered', $manuscript->test_16_embroidered ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <!-- Test 17 -->
                                            <tr>
                                                <td class="text-center fw-bold">17</td>
                                                <td>Whether warp / weft / Pile knitted (.)</td>
                                                <td>
                                                    <select class="form-select border-0" name="test_17_knitted_type">
                                                        <option value="">Select...</option>
                                                        <option value="Warp knitted" {{ old('test_17_knitted_type', $manuscript->test_17_knitted_type ?? '') == 'Warp knitted' ? 'selected' : '' }}>Warp knitted</option>
                                                        <option value="Weft knitted" {{ old('test_17_knitted_type', $manuscript->test_17_knitted_type ?? '') == 'Weft knitted' ? 'selected' : '' }}>Weft knitted</option>
                                                        <option value="Pile knitted" {{ old('test_17_knitted_type', $manuscript->test_17_knitted_type ?? '') == 'Pile knitted' ? 'selected' : '' }}>Pile knitted</option>
                                                        <option value="Not applicable" {{ old('test_17_knitted_type', $manuscript->test_17_knitted_type ?? '') == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Signature Section -->
                            <div class="card card-bordered mb-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-center py-4" width="50%">
                                                    <div class="mb-4" style="height: 60px; border-bottom: 1px solid #000; margin-bottom: 10px;"></div>
                                                    <strong>Signature of QAO/JQAO</strong>
                                                </td>
                                                <td class="text-center py-4" width="50%">
                                                    <div class="mb-4" style="height: 60px; border-bottom: 1px solid #000; margin-bottom: 10px;"></div>
                                                    <strong>Signature of Technical Manager</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="row g-4">
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label class="form-label">Additional Notes/Remarks</label>
                                                <textarea class="form-control" name="remarks" rows="3" 
                                                          placeholder="Enter any additional observations or notes...">{{ old('remarks', $manuscript->remarks ?? '') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status">
                                                    <option value="DRAFT" {{ old('status', $manuscript->status ?? 'DRAFT') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                                    <option value="ACTIVE" {{ old('status', $manuscript->status ?? '') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                                    <option value="COMPLETED" {{ old('status', $manuscript->status ?? '') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                                                    <option value="REVIEW" {{ old('status', $manuscript->status ?? '') == 'REVIEW' ? 'selected' : '' }}>Under Review</option>
                                                </select>
                                            </div>
                                            <div class="form-group mt-3">
                                                <label class="form-label">Priority</label>
                                                <select class="form-select" name="priority">
                                                    <option value="LOW" {{ old('priority', $manuscript->priority ?? 'NORMAL') == 'LOW' ? 'selected' : '' }}>Low</option>
                                                    <option value="NORMAL" {{ old('priority', $manuscript->priority ?? 'NORMAL') == 'NORMAL' ? 'selected' : '' }}>Normal</option>
                                                    <option value="HIGH" {{ old('priority', $manuscript->priority ?? '') == 'HIGH' ? 'selected' : '' }}>High</option>
                                                    <option value="URGENT" {{ old('priority', $manuscript->priority ?? '') == 'URGENT' ? 'selected' : '' }}>Urgent</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                        <a href="{{ route('template_manuscript') }}" class="btn btn-outline-light">
                                            <em class="icon ni ni-arrow-left"></em> Back to List
                                        </a>
                                        <div class="btn-group">
                                            <button type="submit" name="action" value="save_draft" class="btn btn-outline-primary">
                                                <em class="icon ni ni-file-text"></em> Save as Draft
                                            </button>
                                            <button type="submit" name="action" value="save_active" class="btn btn-primary">
                                                <em class="icon ni ni-check-circle"></em> Save & Complete
                                            </button>
                                            @if(isset($manuscript))
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-file-pdf"></em> Export
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('manuscripts.export', ['id' => $manuscript->id, 'format' => 'pdf']) }}">
                                                        <em class="icon ni ni-file-pdf"></em> Export as PDF
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('manuscripts.export', ['id' => $manuscript->id, 'format' => 'excel']) }}">
                                                        <em class="icon ni ni-file-excel"></em> Export as Excel
                                                    </a></li>
                                                </ul>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Alert Messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                                <div class="alert-icon">
                                    <em class="icon ni ni-check-circle"></em>
                                </div>
                                <div class="alert-text">
                                    <strong>Success!</strong> {{ session('success') }}
                                </div>
                                <button class="alert-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                                <div class="alert-icon">
                                    <em class="icon ni ni-cross-circle"></em>
                                </div>
                                <div class="alert-text">
                                    <strong>Error!</strong> {{ session('error') }}
                                </div>
                                <button class="alert-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                                <div class="alert-icon">
                                    <em class="icon ni ni-alert-circle"></em>
                                </div>
                                <div class="alert-text">
                                    <strong>Please fix the following errors:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button class="alert-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-calculate GSM values
            const weightS1 = document.querySelector('input[name="test_9_weight_s1"]');
            const weightS2 = document.querySelector('input[name="test_9_weight_s2"]');
            const gsmS1 = document.querySelector('input[name="test_9_gsm_1"]');
            const gsmS2 = document.querySelector('input[name="test_9_gsm_2"]');
            const gsmAvg = document.querySelector('input[name="test_9_gsm_avg"]');
            const dimension = document.querySelector('select[name="test_9_dimension"]');

            function calculateGSM() {
                const dimValue = dimension.value;
                let multiplier = 1;
                
                if (dimValue === '25 X 25 cm') multiplier = 16; // (100x100)/(25x25) = 16
                if (dimValue === '10 X 10 cm') multiplier = 100; // (100x100)/(10x10) = 100
                
                let gsm1 = 0, gsm2 = 0;
                
                if (weightS1.value && multiplier > 1) {
                    gsm1 = parseFloat(weightS1.value) * multiplier;
                    gsmS1.value = gsm1.toFixed(2);
                }
                if (weightS2.value && multiplier > 1) {
                    gsm2 = parseFloat(weightS2.value) * multiplier;
                    gsmS2.value = gsm2.toFixed(2);
                }
                
                // Calculate average
                if (gsm1 > 0 && gsm2 > 0) {
                    gsmAvg.value = ((gsm1 + gsm2) / 2).toFixed(2);
                } else if (gsm1 > 0) {
                    gsmAvg.value = gsm1.toFixed(2);
                } else if (gsm2 > 0) {
                    gsmAvg.value = gsm2.toFixed(2);
                }
            }

            if (weightS1) weightS1.addEventListener('input', calculateGSM);
            if (weightS2) weightS2.addEventListener('input', calculateGSM);
            if (dimension) dimension.addEventListener('change', calculateGSM);

            // Auto-calculate staple/filament yarn percentages
            function setupPercentageCalculation(stapleField, filamentField) {
                if (!stapleField || !filamentField) return;
                
                stapleField.addEventListener('input', function() {
                    const stapleValue = parseFloat(this.value) || 0;
                    if (stapleValue > 0 && stapleValue <= 100) {
                        filamentField.value = (100 - stapleValue).toFixed(1);
                    }
                });
                
                filamentField.addEventListener('input', function() {
                    const filamentValue = parseFloat(this.value) || 0;
                    if (filamentValue > 0 && filamentValue <= 100) {
                        stapleField.value = (100 - filamentValue).toFixed(1);
                    }
                });
            }

            // Setup percentage calculations for Test 10
            setupPercentageCalculation(
                document.querySelector('input[name="test_10_warp_staple"]'),
                document.querySelector('input[name="test_10_warp_filament"]')
            );
            setupPercentageCalculation(
                document.querySelector('input[name="test_10_weft_staple"]'),
                document.querySelector('input[name="test_10_weft_filament"]')
            );

            // Setup percentage calculations for Test 11
            setupPercentageCalculation(
                document.querySelector('input[name="test_11_warp_texturised"]'),
                document.querySelector('input[name="test_11_warp_non_texturised"]')
            );
            setupPercentageCalculation(
                document.querySelector('input[name="test_11_weft_texturised"]'),
                document.querySelector('input[name="test_11_weft_non_texturised"]')
            );

            // Form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = document.querySelectorAll('input[required], select[required]');
                    let hasErrors = false;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('is-invalid');
                            hasErrors = true;
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });

                    if (hasErrors) {
                        e.preventDefault();
                        alert('Please fill in all required fields.');
                        return false;
                    }
                });
            }

            // Auto-save functionality (optional)
            let autoSaveTimer;
            const formInputs = document.querySelectorAll('input:not([readonly]), select, textarea');
            
            formInputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(autoSaveTimer);
                    // Auto-save after 60 seconds of inactivity
                    autoSaveTimer = setTimeout(autoSaveDraft, 60000);
                });
            });

            function autoSaveDraft() {
                const formData = new FormData(form);
                formData.append('auto_save', 'true');
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Draft auto-saved', 'success');
                    }
                })
                .catch(error => console.log('Auto-save failed:', error));
            }

            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'primary'} border-0 position-fixed top-0 end-0 m-3`;
                toast.style.zIndex = '9999';
                toast.setAttribute('role', 'alert');
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <em class="icon ni ni-${type === 'success' ? 'check' : 'info'}-circle me-2"></em>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                
                document.body.appendChild(toast);
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                toast.addEventListener('hidden.bs.toast', function() {
                    document.body.removeChild(toast);
                });
            }
        });
    </script>

    <style>
        /* Custom table styles to match the original document */
        .table-bordered > :not(caption) > * > * {
            border-width: 1px;
            border-color: #333;
        }
        
        .form-control.border-0:focus,
        .form-select.border-0:focus {
            box-shadow: none;
            border-color: transparent;
        }
        
        .table td {
            vertical-align: top;
            padding: 0.75rem 0.5rem;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 0.75rem 0.5rem;
        }
        
        .card-header.bg-primary {
            background-color: #007bff !important;
            border-bottom: 1px solid #333;
        }
        
        .bg-light {
            background-color: #f8f9fa !important;
        }
        
        .form-control-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .small {
            font-size: 0.875rem;
        }
        
        /* Print styles */
        @media print {
            .btn, .alert, .card:last-child {
                display: none !important;
            }
            
            .card {
                border: 1px solid #333 !important;
                box-shadow: none !important;
            }
            
            .table-bordered > :not(caption) > * > * {
                border-width: 1px !important;
                border-color: #333 !important;
            }
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .nk-block-title {
                font-size: 1.25rem;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn-group .btn {
                margin-bottom: 0.5rem;
                width: 100%;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
        }
        
        /* Enhanced visual hierarchy */
        .nk-block-title {
            font-weight: 700;
            color: #1a1a1a;
        }
        
        .badge.bg-primary {
            font-weight: 600;
        }
        
        .fw-bold {
            font-weight: 600 !important;
        }
        
        /* Form validation styles */
        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        /* Toast positioning */
        .toast {
            min-width: 250px;
        }
    </style>
@endsection