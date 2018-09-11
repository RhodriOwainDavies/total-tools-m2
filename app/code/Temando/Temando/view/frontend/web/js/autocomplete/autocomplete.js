define([
    'jquery'
], function ($) {
    var xhr;
    var baseurl = $('#baseurl').val();
    // get country is default country
    var country;
    $.widget('temando.temando', {
        _create: function () {
            // Disable autofill from web browser
            $("input[id$='city']").attr('autocomplete', 'off');
            $("input[id$='zip']").attr('autocomplete', 'off');
            // autocomplete
            this.element.on('click', function (e) {
                if (e.target.getAttribute('class') === 'location') {
                    $("input[id$='city']").val(e.target.getAttribute('suburb'));
                    $("input[id$='zip']").val(e.target.getAttribute('postcode'));
                    $("#autocomplete-suggestion").remove();
                }
            });

            // trigger keyup event on postcode and suburb field
            this.element.on('keyup', function (e) {
                country = $("select[id='country']").val();

                // only city and postcode input are triggered keyup event
                if (e.target.getAttribute('id').indexOf('city') >= 0 ||
                    e.target.getAttribute('id').indexOf('zip') >= 0) {
                    if ($("#autocomplete-suggestion")) {
                        $("#autocomplete-suggestion").remove();
                    }
                    //var regex = new RegExp("^[a-zA-Z0-9]+$");
                    var regex = new RegExp("^[a-zA-Z0-9\\-\\s]+$");
                    // suburb can be the suburb or postcode
                    var query = $("#" + e.target.getAttribute('id')).val();

                    if (regex.test(query) && e.keyCode != 17) {
                        var param = 'query=' + query + '&country='+country;
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
                                $.each(locations, function (query, location) {
                                    var splitLocation = location.split(',', 2);
                                    var suburb = jQuery.trim(splitLocation[0]);
                                    var postcodeCountry = jQuery.trim(splitLocation[1]);
                                    var countryIndex = postcodeCountry.lastIndexOf(" ");
                                    var postcode = postcodeCountry.substring(0, countryIndex);

                                    suggestionDiv += "<div class='location' postcode='" + postcode +
                                        "' suburb='" + suburb + "'>" + location + "</div>";
                                });
                                suggestionDiv += "</div>";
                                $("#form-validate").append(suggestionDiv);
                            }
                        });
                    }
                }
            });
        }
    });
    return $.temando.temando;
});
