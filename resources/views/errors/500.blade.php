@php
    $code = 500;
    $title = 'Server error';
    $heading = 'Something went wrong';
    $message = 'An unexpected error occurred on our end. We have been notified and are working to fix it. Please try again shortly.';
@endphp

@include('errors.layout')
