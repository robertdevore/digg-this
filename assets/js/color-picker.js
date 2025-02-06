jQuery(document).ready(function($) {
    console.log("Checking if wpColorPicker is available:", $.fn.wpColorPicker); // Debugging log

    if ($.fn.wpColorPicker) {
        $('.digg-this-color-picker').wpColorPicker();
    } else {
        console.error("wpColorPicker is not loaded.");
    }
});
