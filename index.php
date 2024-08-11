<head>
    <style>
        .search_panel {
            border-right: 1px solid grey;
            height: 90%;
            overflow-y: auto;
            background-color: #f5f7f6;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: verdana;
        }

        .images img {
            width: 100px;
            height: 100px;
            float: left;
            margin: 5px;
            border: 1px solid black;
        }

        label,
        input,
        textarea,
        select {
            float: left;
            margin-top: 8px;
            width: 95%;
        }

        textarea {
            height: 150px;
            white-space: pre-wrap;
        }

        td {
            padding: 5px;
            border: 1px solid grey;
        }

        .status {
            width: 79%;
            float: left;
            text-align: left;
            border: 0px solid grey;
            background-color: yellowgreen;
            padding: 6px;
        }

        td.images {
            width: 650px;
        }

        table {
            border-spacing: 0;
            margin: 10px;
        }

        .export-csv-div {
            width: 100%;
            display: flex;
            justify-content: end;
        }
    </style>
    <style type="text/css">
        @font-face {
            font-weight: 400;
            font-style: normal;
            font-family: 'Circular-Loom';

            src: url('https://cdn.loom.com/assets/fonts/circular/CircularXXWeb-Book-cd7d2bcec649b1243839a15d5eb8f0a3.woff2') format('woff2');
        }

        @font-face {
            font-weight: 500;
            font-style: normal;
            font-family: 'Circular-Loom';

            src: url('https://cdn.loom.com/assets/fonts/circular/CircularXXWeb-Medium-d74eac43c78bd5852478998ce63dceb3.woff2') format('woff2');
        }

        @font-face {
            font-weight: 700;
            font-style: normal;
            font-family: 'Circular-Loom';

            src: url('https://cdn.loom.com/assets/fonts/circular/CircularXXWeb-Bold-83b8ceaf77f49c7cffa44107561909e4.woff2') format('woff2');
        }

        @font-face {
            font-weight: 900;
            font-style: normal;
            font-family: 'Circular-Loom';

            src: url('https://cdn.loom.com/assets/fonts/circular/CircularXXWeb-Black-bf067ecb8aa777ceb6df7d72226febca.woff2') format('woff2');
        }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css"
        integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
</head>

<body data-new-gr-c-s-check-loaded="14.1063.0" data-gr-ext-installed="">
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">Ebay Search</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="index.php">
                        <!-- <button class="btn btn-primary"> -->
                        Search Category
                        <!-- </button> -->
                    </a>
                </li>
                <li class="dropdown">
                    <a href="upload_csv.php">
                        <!-- <button class="btn btn-success"> -->
                        Upload CSV
                        <!-- </button> -->
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container-fluid my-3">
        <div class="row w-100">
            <div class="col-lg-3 col-md-4 col-sm-12">
                <div class="search_panel">
                    <form method="post" action="fetchData.php" id="form">
                        <div class="form-group">
                            <label>Category ID:</label>
                            <input type="text" class="form-control" name="categoryID" id="categoryID"
                                placeholder="12345">
                        </div>
                        <div class="form-group">
                            <label>Category Name:</label>
                            <input type="text" name="categoryName" id="categoryName" class="form-control"
                                placeholder="Example: Industrial Automation &amp; Motion Controls/Lab Equipements">
                        </div>
                        <div class="form-group">
                            <label>Exclude Sellers:</label>
                            <textarea name="excludedSellers" id="excludedSellers" class="form-control"
                                rows="6"><?php echo trim(file_get_contents('exclude_sellers.txt')); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Condition:</label>
                            <?php
                            $condition_id = file_get_contents('condition_tag.txt');
                            $condition_id = trim($condition_id);
                            $condition_id = (int) $condition_id;
                            ?>
                            <select class="form-control condition" name="condition" id="condition">
                                <option value="1000" <?php if ($condition_id == 1000) { ?> selected <?php } ?>>NEW
                                </option>
                                <option value="3000" <?php if ($condition_id == 3000) { ?> selected <?php } ?>>USED
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Country:</label>
                            <select class="country form-control" name="loc" id="loc">
                                <?php
                                $countries_line = file_get_contents('include_countries.txt');
                                $country_names = explode(',', $countries_line);
                                $country_names = array_map('trim', $country_names);
                                foreach ($country_names as $name) {
                                    echo "<option value=\"$name\">$name</option>\n";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Start Price:</label>
                            <input type="text" class="form-control" name="start_price" id="start_price" value="300">
                        </div>
                        <div class="form-group">
                            <label>End Price:</label>
                            <input type="text" class="form-control" name="end_price" id="end_price" value="30000">
                        </div>
                        <div class="form-group">
                            <label>Specific Seller:</label>
                            <input type="text" class="form-control" name="spec_seller" id="spec_seller" value="">
                        </div>
                        <div class="form-group">
                            <label>Exclude Products Contaning Words:</label>
                            <textarea name="exclude_words" id="exclude_words" class="form-control" rows="5"><?php
                            $ex_words = file_get_contents('exclude_words.txt');
                            $ex_words = str_replace(PHP_EOL, ' ', $ex_words);
                            //PHP_EOL = PHP_End_Of_Line - would remove new lines too
                            $ex_words = preg_replace('/[\r\n]+/', "\n", $ex_words);
                            $ex_words = preg_replace('/[ \t]+/', ' ', $ex_words);
                            echo $ex_words;
                            ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Exclude Words from Product Title:</label>
                            <textarea class="form-control" name="exclude_words_from_title" id="exclude_words_from_title"
                                rows="2"><?php
                                $ex_words = file_get_contents('exclude_words_from_title.txt');
                                $ex_words = str_replace(PHP_EOL, ' ', $ex_words);
                                //PHP_EOL = PHP_End_Of_Line - would remove new lines too
                                $ex_words = preg_replace('/[\r\n]+/', "\n", $ex_words);
                                $ex_words = preg_replace('/[ \t]+/', ' ', $ex_words);
                                echo $ex_words;
                                ?></textarea>
                        </div>
                        <div class="form-group mt-3">
                            <button type="button" class="btn btn-primary form-control" id="btn_search">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-12">
                <div id="loading" align="center" style="display:none;">
                    <!-- <img src="images/spinner.gif" width="50" height="50" /> <br> -->
                    <i>Processing...</i>
                </div>

                <div id="output_string_results" align="center"> </div>
                <div id="op_results" align="center"> </div>
            </div>
        </div>
    </div>


    <script>
        window.checkOutputString = false;
        $(document).ready(function (e) {
            localStorage.removeItem("currentOffset");

            function doCheckOutputString() {
                if (!checkOutputString) {
                    return;
                }
                $.get('output_string.php',
                    function (data) {
                        $("#output_string_results").html(data);
                    }
                );
                setTimeout(doCheckOutputString, 300);
            }
            function startCheckingOutputString() {
                checkOutputString = true;
                doCheckOutputString();
            }
            function stopCheckingOutputString() {
                checkOutputString = false;
            }
            function auto_downloadCSV(fileName) {
                window.location.href = 'download_csv.php?fname=' + fileName;
            };

            // Change button text back to 'Submit' when any input changes
            $('input, textarea, select').on('input change', function () {
                $("#btn_search").text("Submit");
                localStorage.removeItem("currentOffset");
            });

            //Get the Category Info Listings
            $("#btn_search").click(function (e) {

                var cat_id = $('#categoryID').val();
                if (cat_id == '') {
                    alert('Category ID is Required...');
                    return false; // breaks
                }

                const currentOffset = localStorage.getItem("currentOffset") ?? 0;

                var csv_file = cat_id + '.csv';

                $("#loading").css('display', 'block');

                var categoryName = $('#categoryName').val();

                var excludedSellers = $('#excludedSellers').val();
                var excludeWordsFromTitle = $('#exclude_words_from_title').val();
                var condition = $('#condition').val();
                var country = $('#loc').val();
                var start_price = $('#start_price').val();
                var end_price = $('#end_price').val();
                var spec_seller = $('#spec_seller').val();
                //var wordFilter = $('#wordFilter').val();            
                var exclude_words = $('#exclude_words').val();
                //var maintain = $('#maintain').val();

                var query_string = {
                    action: 'get_ebay_details',
                    cat_id: cat_id,
                    categoryName: categoryName,
                    excludedSellers: excludedSellers,
                    condition: condition,
                    spec_seller: spec_seller,
                    country: country,
                    start_price: start_price,
                    end_price: end_price,
                    exclude_words: exclude_words,
                    excludeWordsFromTitle: excludeWordsFromTitle,
                    currentOffset: currentOffset
                };

                startCheckingOutputString();

                $.post('ajax_functions.php', query_string,
                    function (data) {
                        const parsedData = JSON.parse(data);

                        $("#loading").css('display', 'none');
                        // $("#op_results").html(data);

                        if (parsedData?.success) {
                            if (parsedData?.offset) {
                                localStorage.setItem("currentOffset", parsedData?.offset);
                                $("#btn_search").text("Fetch More");
                            }

                            stopCheckingOutputString();

                            $("#loading").css('display', 'none');

                            //download CSV automatically here
                            setTimeout(function () {
                                auto_downloadCSV(csv_file);
                            }, 100);
                        } else {
                            console.log(parsedData?.error);
                            $("#loading").css('display', 'none');
                        }
                    }
                );
            });
        });
    </script>
</body>