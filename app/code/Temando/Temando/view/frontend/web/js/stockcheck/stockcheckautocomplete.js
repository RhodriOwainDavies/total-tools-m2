define([
    'jquery'
], function ($) {
    var xhr;
    var stockCheckXhr;
    var baseurl = $('#baseurl').val();
    // get country is default country
    var country;
    var sku;
    $.widget('temando.temando', {
        _create: function () {
            // Disable autofill from web browser
            $("input[id$='stock-check-postcode']").attr('autocomplete', 'off');

            // trigger keyup event on postcode field
            this.element.on('keyup', function (e) {
                //$("select[id='country']").val();
                country = "AU";

                if ($("#autocomplete-suggestion")) {
                    $("#autocomplete-suggestion").empty();
                }
                if ($("#stock-check-result")) {
                    $("#stock-check-result").empty();
                }
                var regex = new RegExp("^[a-zA-Z0-9\\-\\s]+$");
                // suburb can be the suburb or postcode
                var query = $("#stock-check-postcode").val();

                if (regex.test(query) && e.keyCode != 17) {
                    $("input[id$='stock-check-postcode']").addClass('avs-active');
                    var param = 'query=' + query + '&country='+country;
                    if (xhr && xhr.readystate != 4) {
                        xhr.abort();
                    }
                    xhr = $.ajax({
                        showLoader: false,
                        url: baseurl + 'temando/api/pcs',
                        data: param,
                        type: "POST",
                        dataType: 'json'
                    }).done(function (data) {
                        var locations = data['suggestions'];
                        $("input[id$='stock-check-postcode']").removeClass('avs-active');
                        if (locations.length > 0) {
                            var suggestionDiv = '';

                            $.each(locations, function (query, location) {
                                var splitLocation = location.split(',', 2);
                                var suburb = jQuery.trim(splitLocation[0]);
                                var postcodeCountry = jQuery.trim(splitLocation[1]);
                                var countryIndex = postcodeCountry.lastIndexOf(" ");
                                var postcode = postcodeCountry.substring(0, countryIndex);

                                suggestionDiv += "<div class='location' postcode='" + postcode +
                                    "' suburb='" + suburb + "'>" + location + "</div>";
                            });

                            $("#autocomplete-suggestion").append(suggestionDiv);

                            $('.location').on('click', function (e) {

                                var chosenCity = e.target.getAttribute('suburb');
                                var chosenPostcode = e.target.getAttribute('postcode');

                                $("#stock-check-postcode").val(chosenCity + ' ' + chosenPostcode);
                                $("#autocomplete-suggestion").empty();//trigger stock check
                                sku = jQuery("div[itemprop='sku']")[0].innerText

                                var postcodeRegex = new RegExp("^[a-zA-Z0-9\\-\\s]+$");
                                // suburb can be the suburb or postcode

                                if (postcodeRegex.test(chosenPostcode)) {
                                    var stockCheckParam = 'postcode=' + chosenPostcode;
                                    stockCheckParam += '&country=' + country;
                                    stockCheckParam += "&sku=" + sku;
                                    if (stockCheckXhr && xhr.readystate != 4) {
                                        stockCheckXhr.abort();
                                    }

                                    stockCheckXhr = $.ajax({
                                        showLoader: true,
                                        url: baseurl + 'temando/product/stockcheck',
                                        data: stockCheckParam,
                                        type: "GET",
                                        dataType: 'json'
                                    }).done(function (data) {
                                        var stockCheckResulItemsDiv = '';
                                        var count = 0;
                                        $.each(data, function (erpId, info) {
                                            var className = 'even';
                                            if (count % 2 !=0) {
                                                className = 'odd';
                                            }
                                            stockCheckResulItemsDiv += '<div class="stock-check-result-item ' + className + '">';
                                            stockCheckResulItemsDiv += '<span class="stock-check-store">' + info.name + '</span>';
                                            stockCheckResulItemsDiv += '<span class="stock-check-separator"> - </span>';
                                            stockCheckResulItemsDiv += '<span class="stock-check-message">' + info.message + '</span>';
                                            stockCheckResulItemsDiv += '<span class="stock-check-stop">.</span>';
                                            stockCheckResulItemsDiv += '</div>';
                                            count++;
                                        });
                                        $("#stock-check-result").append(stockCheckResulItemsDiv);

                                        $("#stock-check-result").on('click', function (e) {
                                            $("#stock-check-result").empty();
                                            $("input[id$='stock-check-postcode']").val('');

                                        });
                                    }).fail(function (error) {
                                        //$("input[id$='stock-check-postcode']").val('');
                                    });
                                }

                            });
                        } else {
                            //search query returned no results, ignore
                        }
                    }).fail(function (error) {
                        //$("input[id$='stock-check-postcode']").removeClass('avs-active');
                    });
                }
            });
        }
    });
    return $.temando.temando;
});
