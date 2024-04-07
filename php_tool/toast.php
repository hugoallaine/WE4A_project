<?php

function successToast($message) {
    echo '<div class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3" id="Toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    '.$message.'
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>';
}

function errorToast($message) {
    echo '<div class="toast align-items-center text-bg-danger border-0 position-fixed bottom-0 end-0 m-3" id="Toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    '.$message.'
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>';
}


?>