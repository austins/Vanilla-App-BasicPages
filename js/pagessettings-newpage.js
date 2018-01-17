jQuery(document).ready(function($) {
    // Map plain text page name to url code.
    $("#Form_Name").keyup(function(event) {
        if ($('#Form_UrlCodeIsDefined').val() == '0') {
            $('#UrlCode').show();
            var val = $(this).val().replace(/[ \/\\&.?;,<>'"]+/g, '-')
            val = val.replace(/\-+/g, '-').toLowerCase();
            $("#Form_UrlCode").val(val);
            $("#UrlCode span").text(val);
        }
    });
    // Make sure not to override any values set by the user.
    $('#UrlCode span').text($('#UrlCode input').val());
    $("#Form_UrlCode").focus(function() {
        $('#Form_UrlCodeIsDefined').val('1')
    });
    $('#UrlCode input, #UrlCode a.Save').hide();

    // Reveal input when "change" button is clicked.
    $('#UrlCode a, #UrlCode span').click(function() {
        $('#UrlCode').find('input,span,a').toggle();
        $('#UrlCode span').text($('#UrlCode input').val());
        $('#UrlCode input').focus();
        return false;
    });

    // Attach autogrow function to page body textarea.
    if ($.autogrow) {
        $('textarea.TextBox').livequery(function() {
            $(this).autogrow();
        });
    }

    // Show advanced options.
    var isAdvancedOptionsClicked = false;

    function showAdvancedOptions() {
        if (!isAdvancedOptionsClicked) {
            $('#AdvancedOptionsToggle').find(".iCheck-helper").trigger('click');
            isAdvancedOptionsClicked = true;
        }

        $('#Form_ShowAdvancedOptions').attr('checked', true);
        $('#AdvancedOptions').show();
    }

    if ($('#AdvancedOptions #Form_Format').val() == gdn.definition('DefaultFormat'))
        $('#Form_ShowAdvancedOptions').attr('checked', false);
    else
        showAdvancedOptions();

    $('#AdvancedOptions input:checked').each(function() {
        if ($(this).is(':checked')) {
            showAdvancedOptions();

            return false;
        }
    });

    $('#Form_ShowAdvancedOptions').change(function() {
        if (this.checked)
            $('#AdvancedOptions').show();
        else
            $('#AdvancedOptions').hide();
    });
});
