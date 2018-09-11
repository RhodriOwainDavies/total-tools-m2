define([
    'jquery'
], function ($) {
    var xhr;
    var baseurl = $('#baseurl').val();
    // get country is default country
    var country =  $("select[id$='_country']").val();
    $.widget('temando.temando', {
        _create: function () {
            // Disable autofill from web browser
            $("input[id$='_city']").attr('autocomplete', 'off');
            $("input[id$='_postcode']").attr('autocomplete', 'off');
            // autocomplete
            this.element.on('click', function (e) {
                if (e.target.getAttribute('class') === 'location') {
                    $("input[id$='_city']").val(e.target.getAttribute('suburb'));
                    $("input[id$='_postcode']").val(e.target.getAttribute('postcode'));
                    $("#autocomplete-suggestion").remove();
                }
            });
            
            // trigger onchange event on country select field
            this.element.on("change", function (e) {
                if (e.target.getAttribute('id').indexOf('country') > 0) {
                    country = $("select[id$='_country']").val();
                    $("#autocomplete-suggestion").remove();
                }
            });
            // trigger keyup event on postcode and suburb field
            this.element.on('keyup', function (e) {
               // only city and postcode input are triggered keyup event
                if (e.target.getAttribute('id').indexOf('city') > 0 ||
                    e.target.getAttribute('id').indexOf('postcode') > 0) {
                    if ($("#autocomplete-suggestion")) {
                        $("#autocomplete-suggestion").remove();
                    }
                    //var regex = new RegExp("^[a-zA-Z0-9]+$");
                    var regex = new RegExp("^[a-zA-Z0-9\\-\\s]+$");
                   // suburb can be the suburb or postcode
                    suburb = $("#" + e.target.getAttribute('id')).val();
                    if (regex.test(suburb) && e.keyCode != 17) {
                        var param = 'query=' + suburb + '&country='+country;
                        if (xhr && xhr.readystate != 4) {
                            xhr.abort();
                        }
                        xhr = $.ajax({
                            showLoader: true,
                            url: baseurl + 'temando/api/pcs',
                            data: param,
                            type: "POST",
                            dataType: 'json'
                        }).done(function (data) {
                            locations = data['suggestions'];
                            if (locations.length > 0) {
                                suggestionDiv = '';
                                var top = $('#' + e.target.getAttribute('id')).offset().top + 35;
                                var left = $('#' + e.target.getAttribute('id')).offset().left;
                                suggestionDiv = "<div id='autocomplete-suggestion' style='top:" + top +
                                    "px; left:" + left + "px'>";
                                $.each(locations, function (suburb, location) {
                                    var splitLocation = location.split(',', 2);
                                    var suburb = jQuery.trim(splitLocation[0]);
                                    var postcodeCountry = jQuery.trim(splitLocation[1]);
                                    var countryIndex = postcodeCountry.lastIndexOf(" ");
                                    var postcode = postcodeCountry.substring(0, countryIndex);
                                    
                                    suggestionDiv += "<div class='location' postcode='" + postcode +
                                        "' suburb='" + suburb + "'>" + location + "</div>";
                                });
                                suggestionDiv += "</div>";
                                $("#edit_form").append(suggestionDiv);
                            }
                        });
                    }
                }
            });
        }
    });
    return $.temando.temando;
});
