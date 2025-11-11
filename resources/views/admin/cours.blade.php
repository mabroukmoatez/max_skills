@extends('layout.master')
@section('title', 'Cours ajoutés')
@section('css')
<style>
   .image {
        display: block !important;
        width: 100% !important;
        height: 200px !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
    }

    .transparent-box {
        height: 100% !important;
        width: 100% !important;
        background-color: rgba(0, 0, 0, 0.2) !important; /* Gray with 50% opacity */
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        z-index: 1 !important; /* Ensure the overlay is above the image */
    }

    .caption {
        color: white !important; /* Text color for the caption */
        font-size: 50px !important; /* Adjust the size of the icon or text */
    }

    .image_cour {
    display: block !important;
    width: 100% !important; 
    height: 200px !important;
    position: relative !important;
    overflow: hidden !important; 
    }

    .image_cour img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important; 
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
    }
</style>
@endsection
@section('main-content')
    <div class="container-fluid">
        <div class="row m-1" style="margin-bottom:20px !important;">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="main-title">Cours ajoutés</h4>
                <a href="{{ route('admin.ajout_cour') }}" class="btn btn-primary" style="padding:9px 31px !important;">
                    Créer un  nouveau cour
                </a>
            </div>
        </div>
        <!-- Blank start -->
        <div class="row">
          @if(session('success'))
              <div class="alert alert-success col-lg-12">
                  {{ session('success') }}
              </div>
          @endif

          @if(session('error'))
              <div class="alert alert-danger col-lg-12">
                  {{ session('error') }}
              </div>
          @endif
            <!-- Default Card start -->
            <div class="col-lg-12 col-lg-8">
                <div class="product-wrapper-grid">
                  <div class="row">
                    <!-- Product box -->
                    @foreach($cours as $cour)
                    <div class="col-xxl-4 col-md-4 col-sm-6" style="margin-bottom: 20px !important;">
                      <div class="card overflow-hidden shadow" style="height: 100%;">
                        <div class="card-body p-0">
                          <div class="product-content-box">
                            <div class="product-grid" onclick="window.location.href='{{ route('admin.ajout_cour') }}?id_cour={{ $cour->id }}'">
                              <div class="product-image left-main-img img-box">
                                @if($cour->visibility == 2)
                                <a href="#" class="image">
                                  <img src="https://maxskills.tn/{{ $cour->path_banner }}" alt="">
                                  <div class="transparent-box">
                                    <div class="caption">
                                      <i class="fa-solid fa-ban fa-fw"></i>
                                    </div>
                                  </div>
                                </a>
                                @elseif($cour->visibility == 0)
                                <a href="#" class="image">
                                  <img src="https://maxskills.tn/{{ $cour->path_banner }}" alt="">
                                  <div class="transparent-box">
                                    <div class="caption">
                                      <i class="fa-solid fa-lock fa-fw"></i>
                                    </div>
                                  </div>
                                </a>
                                @else
                                <a href="#" class="image_cour">
                                  <img src="https://maxskills.tn{{ $cour->path_banner }}" alt="">
                                </a>
                                @endif
                                <div class="col-11 product-links" style="display: flex; flex-direction: row; flex-wrap: nowrap; align-content: flex-start; justify-content: space-between; align-items: flex-start; ">
                                @if($cour->visibility != 2)
                                <div>
                                  <a href="https://maxskills.tn/formation/cour/{{ $cour->id }}" onclick="event.stopPropagation()" target="_blank" class="bg-success h-30 w-30 d-flex-center b-r-20 border-0">
                                    <i class="ti ti-eye" style="color: #fff;"></i>
                                  </a>
                                  
                                </div>
                                <div>
                                   <form action="{{ route('admin.delete_cour', ['id' => $cour->id]) }}" method="POST" class="d-inline">
                                      @csrf
                                      <button type="submit" class="bg-danger h-30 w-30 d-flex-center b-r-20 border-0">
                                          <i class="ti ti-trash-x text-light"></i>
                                      </button>
                                    </form> 
                                </div>
                                @endif
                                </div>
                              </div>
    
                            </div>
                            <div class="p-3">
                              <div class="d-flex justify-content-between">
                                @php
                                  $color = "#F8994F";
                                  if($cour->visibility == 0){
                                    $color = "#4C4C4C";
                                  }
                                  if($cour->visibility == 2){
                                    $color = "#dc3545";
                                  }

                                @endphp
                                <span class="d-flex-center b-r-15 f-s-10" style="margin-bottom: 15px !important;background-color: {{ $color }}!important;color:#fff !important;">
                                  <div style="padding-right: 6px;padding-left: 6px;">
                                    @if($cour->visibility == 1)
                                      Publié
                                    @elseif($cour->visibility == 0)
                                      Privé
                                    @else
                                      Archivée
                                    @endif
                                  </div>
                                </span>
                              </div>
                              <div class="d-flex justify-content-between align-items-center" style="margin-bottom:15px !important;">
                                <a href="{{ route('admin.ajout_cour') }}?id_cour={{ $cour->id }}" class="m-0 f-s-15 f-w-400" stylle="color:#000 !important" title="{{ $cour->title }}">{{ Str::limit($cour->title, 50, '...') }}</a>
                              </div>
                              <div class="profile-friends">
                                <div class="d-flex align-items-center">
                                  <div class="h-40 w-40 d-flex-center b-r-50 overflow-hidden bg-dark">
                                    <img src="{{ asset('assets/images/ai_avtar/2.jpg') }}" alt="image" class="img-fluid">
                                  </div>
                                  <div class="flex-grow-1 ps-2">
                                    <div class="fw-medium"> {{ $cour->user->firstname.' '.$cour->user->name }}</div>
                                    <div class="text-muted f-s-12">{{ $cour->user->niveau ?? '' }}</div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                    @endforeach
                    <!-- Product box -->
                  </div>
                </div>

              </div>

            <!-- Default Card end -->
        </div>
        <!-- Blank end -->
    </div>
@endsection

@section('script')

@endsection

