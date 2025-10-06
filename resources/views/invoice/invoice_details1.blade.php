@extends('layouts.app_back')
@section('content')
	
	 <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="components-preview wide-xl mx-auto">
                        <div class="nk-block nk-block-lg">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <div class="nk-block-head-content">
                                	@php
                                	$firstsample = $samples->first();
                                	@endphp
                                    <h3 class="nk-block-title page-title">Customer Details
                                        <strong class="text-primary small">
                                        {{ $firstsample['customer']['m07_name'] }}</strong>
                                    </h3>
                                    <div class="nk-block-des text-soft">
                                        <ul class="list-inline">
                                            <li>Person: <span
                                                    class="text-base">{{ $firstsample['customer']['m07_contact_person'] }}</span>
                                            </li>
                                            <li>GST: <span
                                                    class="text-base">{{ $firstsample['customer']['m07_gst'] }}</span>
                                            </li>
                                           
                                     
                                        </ul>
                                    </div>
                                </div>
                                <a href="{{ url()->previous() }}" class="btn btn-primary">
                                    <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                                </a>
                            </div>
                        </div>
                        <div class="nk-block">
                            <div class="invoice">
                                <div class="invoice-action">
                                   
                                </div><!-- .invoice-actions -->
                                <div class="invoice-wrap">
                                		@foreach($samples as $sample)
                                    <div class="invoice-head">
                                    
                                        <div class="invoice-contact">
                                            <span class="overline-title">Sample Details</span>
                                            <div class="invoice-contact-info">
                                                <h4 class="title">#{{ $sample->tr04_reference_id }}</h4>
                                               <b>Lab Sample:</b><span> {{ $sample->labSample['m14_name'] ?? 'N/A' }}</span>
                                                    <b>Description: 
                                                          </b><span>{{ $sample->tr04_sample_description ?? 'N/A' }}</span>
                                            </div>
                                        </div>

                                        <div class="invoice-desc">
                                            <ul class="list-plain">
                                                <li class="invoice-id"><span>Grand Total</span>:<span>{{ number_format($sample->tr04_total_charges, 2) }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                      
                                    </div><!-- .invoice-head -->
                                      @endforeach

                                      <div class="invoice-foot">
    <div class="invoice-summary">
        <ul class="list-plain">
            <li>
                <span><strong>Total Amount:</strong></span>
                <span><strong>{{ number_format($totalAmount, 2) }}</strong></span>
            </li>
        </ul>
    </div>
</div>

                                        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    




@endsection