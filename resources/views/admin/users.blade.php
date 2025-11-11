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
        padding: 20px;
        border: 1px solid #888;
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
            max-width: 50% !important;
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
        padding: 0px 8px;
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
    .form-control:focus { 
        box-shadow: 0 0 0 0.1px rgba(160, 160, 176, 0.55);
    }

    .form-select:focus { 
        box-shadow: 0 0 0 0.1px rgba(160, 160, 176, 0.55);
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('main-content')
    <div class="container-fluid">
        <div class="row m-1" style="margin-bottom:20px !important;">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="main-title">Utilisateurs</h4>
                
                <button id="btnAddAgentFirst" class="btn mb-3" style="background-color:#F8994F !important;color:#fff !important;padding:9px 31px !important;" onclick="AddAgent()">
                   <i class="ti ti-users"></i>
                    Ajouter un utilisateur
                </button>
            </div>
            <div class="row">
                <p>Mettez à jour les informations pour vos utilisateurs</p>
            </div>
        </div>
        <!-- Blank start -->
        <div class="row">
            <!-- Default Card start -->
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @livewire('users')
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

    <!--js-->
    <script src="{{asset('assets/js/select.js')}}"></script>
    <script>
        function AddAgent(){
            btn = document.getElementById('btnAddAgent');
            if(btn){
                btn.click();
            }
        }
    </script>
@endsection