jQuery(document).ready(function($) {
    $('.second_part').hide();
    var options = [];
    // When Velgfylke dropdown changes
    $('#velgfylke').change(function() {
        var selectedValue = $(this).val().toLowerCase(); // Convert selectedValue to lowercase
        

        // Iterate over csvData keys and compare them in lowercase
       
        $.each(csvData, function(key, value) {
          
            if (key.toLowerCase() === selectedValue) {
                options = value;
                return false; // Break the loop
            }
        });

        var $butikk = $('#butikk');
        $butikk.empty();
        // Add a placeholder option
        if (options.length > 0) {
            $butikk.append($('<option>', {
                text: options[0].placeholder, // Use the first child's text as the placeholder
                value: '',
                disabled: true,
                selected: true
            }));
        }
        
        options.forEach(function(option) {
            $butikk.append($('<option>', {
                value: option.value,
                text: option.child
            }));
            
        });
    });

    // When Butikk dropdown changes
    $('#butikk').change(function() {
        var selectedValue = $(this).val();
        var selectedText = $('#butikk option:selected').text(); 
        
        var selectedOption = options.find(function(option) {
           
            return option.child.toLowerCase() === selectedText.toLowerCase();
        });
        console.log(selectedOption);
         if (selectedOption) {
            $('#custom_text').text(selectedOption.custom_text);
         }
        if (selectedValue.startsWith('http://') || selectedValue.startsWith('https://')) {
            window.location.href = selectedValue;
        } else if (selectedValue.includes('@')) {
            $('.second_part').show();
            $('#contact-form').attr('action', selectedValue);
        } else {
           $('.second_part').hide();
        }
    });

    // Initially hide name and message fields
    $('#name, #message').closest('.form-row').hide();
});
