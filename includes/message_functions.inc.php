<?php 

function successMessage(string $text): string {
    return '<p class="success">' . $text . '</p>';
}

function errorMessage(string $text): string {
    return '<p class="error">' . $text . '</p>';
}

?>