jQuery(document).ready(function ($) {
    // Access PHP data passed via wp_localize_script
    const data = phpData;

    // Process each select element based on PHP keys
    $.each(data, function (key, options) {
        const $select = $(`select[name="${key}"]`);
        if ($select.length) {
            // Add change event listener to dynamically show/hide divs
            $select.on('change', function () {
                const selectedValue = $(this).val();

                // Loop through options
                $.each(options, function (index, option) {
                    const optionClass = '.iump-form-'+option.name;
                    // console.log(option.value + "---" + selectedValue);
                    if(option.value === selectedValue)
                    {
                        console.log(option.name);
                        $(optionClass).removeClass('hidden');
                        $(optionClass).addClass('nothidden');
                    }
                    else
                    {
                        $(optionClass).addClass('hidden');
                        $(optionClass).removeClass('nothidden');
                    }
                });
            });
        }
    });
});

