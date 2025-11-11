@extends('layout.master')
@section('title', 'Apprenants')
@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/filepond/filepond.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/filepond/image-preview.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/toastify/toastify.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/select/select2.min.css')}}">
<style>
    .modal {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px 20px 0px 20px;
        border: 1px solid #888;
        border-radius : 26px !important;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover {
        color: black;
        cursor: pointer;
    }
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 35% !important;
        }
    }
    .pagination-nav {
        position: static !important; /* Remove absolute positioning */
        left: auto !important; /* Reset right positioning */
        background-color: transparent !important; /* Reset background color */
    }
    .pagination-nav .pagination {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        gap: 8px; /* Space between pagination items */
    }

    .pagination-nav .page-item {
        display: inline-block;
    }

    .pagination-nav .page-link {
        display: block;
        padding: 2px 11px 2px 11px !important;
        font-size: 15px !important;
        border: 1px solid #ddd;
        border-radius: 5px;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .pagination-nav .page-link:hover {
        background-color: #f8f9fa;
        border-color: #ddd;
    }

    .pagination-nav .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .pagination-nav .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #f8f9fa;
        border-color: #ddd;
    }

    #paymentFileLoading {
        position: relative;
    }

    #paymentFileLoading .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    #paymentFileLoading p {
        margin-top: 10px;
        font-size: 14px;
        color: #333;
    }
    .input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-group-text {
        background-color: #fff; /* Match input background */
        border: 1px solid #ced4da; /* Match input border */
        border-right: none; /* Remove right border to blend with input */
        padding: 0.375rem 0.75rem; /* Match input padding */
    }

    .app-form.app-icon-form .form-control:focus {
        box-shadow :none !important;
        border : 1px solid rgba(var(--secondary),.4) !important;
        border-color: rgba(var(--secondary),.4) !important;
    }
    .form-control:focus {
         border-color:rgb(150, 150, 150) !important;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('main-content')
    <div class="container-fluid">
        <div class="row m-1" style="margin-bottom:20px !important;">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="main-title">Apparenent inscits</h4>
                
            </div>
        </div>
        <!-- Blank start -->
        <div class="row">
            <!-- Default Card start -->
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @livewire('clients')
                    </div>
                </div>
            </div>
            <!-- Default Card end -->
        </div>
   
        <!-- Blank end -->
    </div>
@endsection

@section('script')
    <!-- filepond -->
    <script src="{{asset('assets/vendor/filepond/file-encode.min.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/validate-size.min.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/validate-type.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/exif-orientation.min.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/image-preview.min.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/filepond.min.js')}}"></script>

    <script src="{{asset('assets/vendor/notifications/toastify-js.js')}}"></script>
    <script src="{{asset('assets/vendor/toastify/toastify.js')}}"></script>

    <!-- phosphor js -->
    <script src="{{asset('assets/vendor/phosphor/phosphor.js')}}"></script>
    <script src="{{asset('assets/vendor/sortable/Sortable.min.js')}}"></script>
    
    <!-- select2 -->
    <script src="{{asset('assets/vendor/select/select2.min.js')}}"></script>

    <!-- js -->
    <script src="{{asset('assets/js/select.js')}}"></script>
@endsection