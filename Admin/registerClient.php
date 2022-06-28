<?php
    // Client Registration Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Admin.
    if ($tempLoginCheck != 4) {
        header("Location: /index.php");
        exit;
    }

    $tempPFP = $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempCountry = $tempAddress = "";
    $registrationMsg = "";
    $passRegistration = false;

    // Registration attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempName = (isset($_POST["Username"])) ? cleanInput($_POST["Username"]): "";
        $tempRName = (isset($_POST["RealName"])) ? cleanInput($_POST["RealName"]): "";
        $tempEmail = (isset($_POST["Email"])) ? cleanInput($_POST["Email"]): "";
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";
        $tempRPass = (isset($_POST["ReconfirmPassword"])) ? cleanInput($_POST["ReconfirmPassword"]): "";
        $tempCountry = (isset($_POST["Country"])) ? cleanInput($_POST["Country"]): "";
        $tempAddress = (isset($_POST["Address"])) ? cleanInput($_POST["Address"]): "";

        $tempID = $tempHash = "";

        if (
            empty($tempName) ||
            empty($tempRName) ||
            empty($tempEmail) ||
            empty($tempPass) ||
            empty($tempRPass) ||
            empty($tempCountry) ||
            empty($tempAddress) 
        ) {
            $registrationMsg = "* Fill in ALL Fields! *";
            $passRegistration = false;
        }
        else {
            // Set to true at first.
            $passRegistration = true;

            // Check Username.
            if (checkExistUsername($conn, $tempName)) {
                $registrationMsg = "* Username is used by another user! *";
                $passRegistration = false;
            }

            // Check Email.
            if ($passRegistration && checkExistEmail($conn, $tempEmail)) {
                $registrationMsg = "* Email is used by another user! *";
                $passRegistration = false;
            }

            // Check Password.
            if ($passRegistration && empty($tempHash = checkReconfirmPassword($tempPass, $tempRPass))) {
                $registrationMsg = "* Reenter the EXACT SAME Password! *";
                $passRegistration = false;
            }

            // Insert to DB.
            if ($passRegistration) {
                
                // Insert to User table with UserType CO.
                $query = "INSERT INTO `User`(`Username`, `Email`, `PasswordHash`, `RealName`, `UserType`)";
                $query .= " VALUES ('$tempName','$tempEmail','$tempHash','$tempRName','CL')";

                $rs = $conn->query($query);
                if (!$rs) {
                    $registrationMsg = "* Fail to insert to User table! *";
                    $passRegistration = false;
                }

                // Insert to Client table.
                if ($passRegistration) {
                    $passRegistration = false;

                    // Get UserID.
                    $tempID = $conn->insert_id;

                    // Process image path
                    if($_FILES["ClientPfp"]["error"] == 0) {
                        date_default_timezone_set('Asia/Kuala_Lumpur');

                        $tempPFP = explode(".", $_FILES["ClientPfp"]["name"]);
                        $newfilename = $tempID . "_" . date('Y-m-d') . "_" . round(microtime(true)) . "." . end($tempPFP);
                        $filepath = "../img/client/" . $newfilename;
                    } else {
                        $filepath = "../img/client/default_client.jpg";
                    }
                    
                    // Insert with the obtained UserID.
                    $query = "INSERT INTO `Client`(`UserID`, `Country`,`Address`,`Photo`)";
                    $query .= " VALUES ('$tempID','$tempCountry','$tempAddress','$filepath')";
                    $rs = $conn->query($query);

                    if (!$rs) {
                        $registrationMsg = "* Fail to insert to Client table! *";
                    }
                    else {
                        if ($_FILES["ClientPfp"]["error"] == 0) {
                            move_uploaded_file($_FILES["ClientPfp"]["tmp_name"], $filepath);
                        }

                        $passRegistration = true;
                    }
                }

                // Check if the data is successfully inserted.
                if ($passRegistration) {
                    // Reset to empty.
                    $tempPFP = $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempEDate = $tempCountry = $tempAddress = "";
                    $registrationMsg = "* User is successfully registered! *";
                }
            }
        }
    }
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Client Registration Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="/css/form.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-colors-vivid.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">

        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(function(){
                $("#Country").select2();
            }); 
        </script>
    </head>

    <body>
        <header>
            <div class="maintheme w3-container">
                <h4 style="font-size: 36px">Admin: Client Registration Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Client Sign Up:</h1>
                </div>
                <div id="formContentW2">
                    <br>
                    <img class="fadeIn first" src="https://png.pngtree.com/png-vector/20190721/ourlarge/pngtree-business-meeting-with-client-illustration-concept-modern-flat-design-concept-png-image_1567633.jpg" id="icon" alt="Comp Icon" />
                    <br>
                    <form method="post" action="/Admin/registerClient.php" enctype="multipart/form-data">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <span class="<?php
                                        echo(($passRegistration) ? "success": "error");
                                    ?>-message"><?php
                                        echo($registrationMsg);
                                    ?></span>
                                </td>
                            </tr>

                            <tr class="fadeIn second">
                                <!-- Profile Pic -->
                                <td colspan="2">
                                    <div>
                                        <label for="ClientPfp">
                                            Profile Picture:
                                        </label><br>
                                        <input type="file" id="ClientPfp" name="ClientPfp" accept="image/*">
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn third">
                                <!-- Username -->
                                <td>
                                    <div>
                                        <label for="Username">
                                            Username:
                                        </label><br>
                                        <input id="Username" type="text" name="Username" value="<?php
                                            echo($tempName);
                                        ?>" placeholder="Username" required>
                                    </div>
                                </td>

                                <!-- RealName -->
                                <td>
                                    <div>
                                        <label for="RealName">
                                            Full Name:
                                        </label><br>
                                        <input id="RealName" type="text" name="RealName" value="<?php
                                            echo($tempRName);
                                        ?>" placeholder="Full Name" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fourth">
                                <!-- Email -->
                                <td>
                                    <div>
                                        <label for="Email">
                                            Email:
                                        </label><br>
                                        <input id="Email" type="email" name="Email" value="<?php
                                            echo($tempEmail);
                                        ?>" placeholder="abc@email.com" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fifth">
                                <!-- Address -->
                                <td colspan="2">
                                    <div>
                                        <label for="Address">
                                            Address:
                                        </label><br>
                                        <textarea id="Address" name="Address" placeholder="Address" required><?php
                                            echo($tempAddress);
                                        ?></textarea>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn sixth">
                                <!-- Country Select -->
                                <td colspan="2">
                                    <div>
                                        <label for="Country">
                                            Country:
                                        </label><br>
                                        <select id="Country" type="select" name="Country" placeholder="Country" required>
                                            <option value="">Select your country</option>
                                            <option style="text-align:left;" value="Afghanistan">Afghanistan</option>
                                            <option style="text-align:left;" value="Albania">Albania</option>
                                            <option style="text-align:left;" value="Algeria">Algeria</option>
                                            <option style="text-align:left;" value="Andorra">Andorra</option>
                                            <option style="text-align:left;" value="Angola">Angola</option>
                                            <option style="text-align:left;" value="Antigua and Barbuda">Antigua and Barbuda</option>
                                            <option style="text-align:left;" value="Argentina">Argentina</option>
                                            <option style="text-align:left;" value="Armenia">Armenia</option>
                                            <option style="text-align:left;" value="Australia">Australia</option>
                                            <option style="text-align:left;" value="Austria">Austria</option>
                                            <option style="text-align:left;" value="Azerbaijan">Azerbaijan</option>
                                            <option style="text-align:left;" value="The Bahamas">The Bahamas</option>
                                            <option style="text-align:left;" value="Bahrain">Bahrain</option>
                                            <option style="text-align:left;" value="Bangladesh">Bangladesh</option>
                                            <option style="text-align:left;" value="Barbados">Barbados</option>
                                            <option style="text-align:left;" value="Belarus">Belarus</option>
                                            <option style="text-align:left;" value="Belgium">Belgium</option>
                                            <option style="text-align:left;" value="Belize">Belize</option>
                                            <option style="text-align:left;" value="Benin">Benin</option>
                                            <option style="text-align:left;" value="Bhutan">Bhutan</option>
                                            <option style="text-align:left;" value="Bolivia">Bolivia</option>
                                            <option style="text-align:left;" value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                            <option style="text-align:left;" value="Botswana">Botswana</option>
                                            <option style="text-align:left;" value="Brazil">Brazil</option>
                                            <option style="text-align:left;" value="Brunei">Brunei</option>
                                            <option style="text-align:left;" value="Bulgaria">Bulgaria</option>
                                            <option style="text-align:left;" value="Burkina Faso">Burkina Faso</option>
                                            <option style="text-align:left;" value="Burundi">Burundi</option>
                                            <option style="text-align:left;" value="Cabo Verde">Cabo Verde</option>
                                            <option style="text-align:left;" value="Cambodia">Cambodia</option>
                                            <option style="text-align:left;" value="Cameroon">Cameroon</option>
                                            <option style="text-align:left;" value="Canada">Canada</option>
                                            <option style="text-align:left;" value="Central African Republic">Central African Republic</option>
                                            <option style="text-align:left;" value="Chad">Chad</option>
                                            <option style="text-align:left;" value="Chile">Chile</option>
                                            <option style="text-align:left;" value="China">China</option>
                                            <option style="text-align:left;" value="Comoros">Comoros</option>
                                            <option style="text-align:left;" value="Colombia">Colombia</option>
                                            <option style="text-align:left;" value="Congo, Democratic Republic of the">Congo, Democratic Republic of the</option>
                                            <option style="text-align:left;" value="Congo, Republic of the">Congo, Republic of the</option>
                                            <option style="text-align:left;" value="Costa Rica">Costa Rica</option>
                                            <option style="text-align:left;" value="Côte d’Ivoire">Côte d’Ivoire</option>
                                            <option style="text-align:left;" value="Croatia">Croatia</option>
                                            <option style="text-align:left;" value="Cuba">Cuba</option>
                                            <option style="text-align:left;" value="Cyprus">Cyprus</option>
                                            <option style="text-align:left;" value="Czech Republic">Czech Republic</option>
                                            <option style="text-align:left;" value="Denmark">Denmark</option>
                                            <option style="text-align:left;" value="Djibouti">Djibouti</option>
                                            <option style="text-align:left;" value="Dominica">Dominica</option>
                                            <option style="text-align:left;" value="Dominican Republic">Dominican Republic</option>
                                            <option style="text-align:left;" value="East Timor (Timor-Leste)">East Timor (Timor-Leste)</option>
                                            <option style="text-align:left;" value="Ecuador">Ecuador</option>
                                            <option style="text-align:left;" value="Egypt">Egypt</option>
                                            <option style="text-align:left;" value="El Salvador">El Salvador</option>
                                            <option style="text-align:left;" value="Equatorial Guinea">Equatorial Guinea</option>
                                            <option style="text-align:left;" value="Eritrea">Eritrea</option>
                                            <option style="text-align:left;" value="Estonia">Estonia</option>
                                            <option style="text-align:left;" value="Eswatini">Eswatini</option>
                                            <option style="text-align:left;" value="Ethiopia">Ethiopia</option>
                                            <option style="text-align:left;" value="Fiji">Fiji</option>
                                            <option style="text-align:left;" value="Finland">Finland</option>
                                            <option style="text-align:left;" value="France">France</option>
                                            <option style="text-align:left;" value="Gabon">Gabon</option>
                                            <option style="text-align:left;" value="The Gambia">The Gambia</option>
                                            <option style="text-align:left;" value="Georgia">Georgia</option>
                                            <option style="text-align:left;" value="Germany">Germany</option>
                                            <option style="text-align:left;" value="Ghana">Ghana</option>
                                            <option style="text-align:left;" value="Greece">Greece</option>
                                            <option style="text-align:left;" value="Grenada">Grenada</option>
                                            <option style="text-align:left;" value="Guatemala">Guatemala</option>
                                            <option style="text-align:left;" value="Guinea">Guinea</option>
                                            <option style="text-align:left;" value="Guinea-Bissau">Guinea-Bissau</option>
                                            <option style="text-align:left;" value="Guyana">Guyana</option>
                                            <option style="text-align:left;" value="Haiti">Haiti</option>
                                            <option style="text-align:left;" value="Honduras">Honduras</option>
                                            <option style="text-align:left;" value="Hungary">Hungary</option>
                                            <option style="text-align:left;" value="Iceland">Iceland</option>
                                            <option style="text-align:left;" value="India">India</option>
                                            <option style="text-align:left;" value="Indonesia">Indonesia</option>
                                            <option style="text-align:left;" value="Iran">Iran</option>
                                            <option style="text-align:left;" value="Iraq">Iraq</option>
                                            <option style="text-align:left;" value="Ireland">Ireland</option>
                                            <option style="text-align:left;" value="Israel">Israel</option>
                                            <option style="text-align:left;" value="Italy">Italy</option>
                                            <option style="text-align:left;" value="Jamaica">Jamaica</option>
                                            <option style="text-align:left;" value="Japan">Japan</option>
                                            <option style="text-align:left;" value="Jordan">Jordan</option>
                                            <option style="text-align:left;" value="Kazakhstan">Kazakhstan</option>
                                            <option style="text-align:left;" value="Kenya">Kenya</option>
                                            <option style="text-align:left;" value="Kiribati">Kiribati</option>
                                            <option style="text-align:left;" value="Korea, North">Korea, North</option>
                                            <option style="text-align:left;" value="Korea, South">Korea, South</option>
                                            <option style="text-align:left;" value="Kosovo">Kosovo</option>
                                            <option style="text-align:left;" value="Kuwait">Kuwait</option>
                                            <option style="text-align:left;" value="Kyrgyzstan">Kyrgyzstan</option>
                                            <option style="text-align:left;" value="Laos">Laos</option>
                                            <option style="text-align:left;" value="Latvia">Latvia</option>
                                            <option style="text-align:left;" value="Lebanon">Lebanon</option>
                                            <option style="text-align:left;" value="Lesotho">Lesotho</option>
                                            <option style="text-align:left;" value="Liberia">Liberia</option>
                                            <option style="text-align:left;" value="Libya">Libya</option>
                                            <option style="text-align:left;" value="Liechtenstein">Liechtenstein</option>
                                            <option style="text-align:left;" value="Lithuania">Lithuania</option>
                                            <option style="text-align:left;" value="Luxembourg">Luxembourg</option>
                                            <option style="text-align:left;" value="Madagascar">Madagascar</option>
                                            <option style="text-align:left;" value="Malawi">Malawi</option>
                                            <option style="text-align:left;" value="Malaysia">Malaysia</option>
                                            <option style="text-align:left;" value="Maldives">Maldives</option>
                                            <option style="text-align:left;" value="Mali">Mali</option>
                                            <option style="text-align:left;" value="Malta">Malta</option>
                                            <option style="text-align:left;" value="Marshall Islands">Marshall Islands</option>
                                            <option style="text-align:left;" value="Mauritania">Mauritania</option>
                                            <option style="text-align:left;" value="Mauritius">Mauritius</option>
                                            <option style="text-align:left;" value="Mexico">Mexico</option>
                                            <option style="text-align:left;" value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                                            <option style="text-align:left;" value="Moldova">Moldova</option>
                                            <option style="text-align:left;" value="Monaco">Monaco</option>
                                            <option style="text-align:left;" value="Mongolia">Mongolia</option>
                                            <option style="text-align:left;" value= "Montenegro">Montenegro</option>
                                            <option style="text-align:left;" value="Morocco">Morocco</option>
                                            <option style="text-align:left;" value="Mozambique">Mozambique</option>
                                            <option style="text-align:left;" value="Myanmar (Burma)">Myanmar (Burma)</option>
                                            <option style="text-align:left;" value="Namibia">Namibia</option>
                                            <option style="text-align:left;" value="Nauru">Nauru</option>
                                            <option style="text-align:left;" value="Nepal">Nepal</option>
                                            <option style="text-align:left;" value="Netherlands">Netherlands</option>
                                            <option style="text-align:left;" value="New Zealand">New Zealand</option>
                                            <option style="text-align:left;" value="Nicaragua">Nicaragua</option>
                                            <option style="text-align:left;" value="Niger">Niger</option>
                                            <option style="text-align:left;" value="Nigeria">Nigeria</option>
                                            <option style="text-align:left;" value="North Macedonia">North Macedonia</option>
                                            <option style="text-align:left;" value="Norway">Norway</option>
                                            <option style="text-align:left;" value="Oman">Oman</option>
                                            <option style="text-align:left;" value="Pakistan">Pakistan</option>
                                            <option style="text-align:left;" value="Palau">Palau</option>
                                            <option style="text-align:left;" value="Panama">Panama</option>
                                            <option style="text-align:left;" value="Papua New Guinea">Papua New Guinea</option>
                                            <option style="text-align:left;" value="Paraguay">Paraguay</option>
                                            <option style="text-align:left;" value="Peru">Peru</option>
                                            <option style="text-align:left;" value="Philippines">Philippines</option>
                                            <option style="text-align:left;" value="Poland">Poland</option>
                                            <option style="text-align:left;" value="Portugal">Portugal</option>
                                            <option style="text-align:left;" value="Qatar">Qatar</option>
                                            <option style="text-align:left;" value="Romania">Romania</option>
                                            <option style="text-align:left;" value="Russia">Russia</option>
                                            <option style="text-align:left;" value="Rwanda">Rwanda</option>
                                            <option style="text-align:left;" value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                            <option style="text-align:left;" value="Saint Lucia">Saint Lucia</option>
                                            <option style="text-align:left;" value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                            <option style="text-align:left;" value="Samoa">Samoa</option>
                                            <option style="text-align:left;" value="San Marino">San Marino</option>
                                            <option style="text-align:left;" value="Sao Tome and Principe">Sao Tome and Principe</option>
                                            <option style="text-align:left;" value="Saudi Arabia">Saudi Arabia</option>
                                            <option style="text-align:left;" value="Senegal">Senegal</option>
                                            <option style="text-align:left;" value="Serbia">Serbia</option>
                                            <option style="text-align:left;" value="Seychelles">Seychelles</option>
                                            <option style="text-align:left;" value="Sierra Leone">Sierra Leone</option>
                                            <option style="text-align:left;" value="Singapore">Singapore</option>
                                            <option style="text-align:left;" value="Slovakia">Slovakia</option>
                                            <option style="text-align:left;" value="Slovenia">Slovenia</option>
                                            <option style="text-align:left;" value="Solomon Islands">Solomon Islands</option>
                                            <option style="text-align:left;" value="Somalia">Somalia</option>
                                            <option style="text-align:left;" value="South Africa">South Africa</option>
                                            <option style="text-align:left;" value="Spain">Spain</option>
                                            <option style="text-align:left;" value="Sri Lanka">Sri Lanka</option>
                                            <option style="text-align:left;" value="Sudan">Sudan</option>
                                            <option style="text-align:left;" value="Sudan, South">Sudan, South</option>
                                            <option style="text-align:left;" value="Suriname">Suriname</option>
                                            <option style="text-align:left;" value="Sweden">Sweden</option>
                                            <option style="text-align:left;" value="Switzerland">Switzerland</option>
                                            <option style="text-align:left;" value="Syria">Syria</option>
                                            <option style="text-align:left;" value="Taiwan">Taiwan</option>
                                            <option style="text-align:left;" value="Tajikistan">Tajikistan</option>
                                            <option style="text-align:left;" value="Tanzania">Tanzania</option>
                                            <option style="text-align:left;" value="Thailand">Thailand</option>
                                            <option style="text-align:left;" value="Togo">Togo</option>
                                            <option style="text-align:left;" value="Tonga">Tonga</option>
                                            <option style="text-align:left;" value="Trinidad and Tobago">Trinidad and Tobago</option>
                                            <option style="text-align:left;" value="Tunisia">Tunisia</option>
                                            <option style="text-align:left;" value="Turkey">Turkey</option>
                                            <option style="text-align:left;" value="Turkmenistan">Turkmenistan</option>
                                            <option style="text-align:left;" value="Tuvalu">Tuvalu</option>
                                            <option style="text-align:left;" value="Uganda">Uganda</option>
                                            <option style="text-align:left;" value="Ukraine">Ukraine</option>
                                            <option style="text-align:left;" value="United Arab Emirates">United Arab Emirates</option>
                                            <option style="text-align:left;" value="United Kingdom">United Kingdom</option>
                                            <option style="text-align:left;" value="United States">United States</option>
                                            <option style="text-align:left;" value="Uruguay">Uruguay</option>
                                            <option style="text-align:left;" value="Uzbekistan">Uzbekistan</option>
                                            <option style="text-align:left;" value="Vanuatu">Vanuatu</option>
                                            <option style="text-align:left;" value="Vatican City">Vatican City</option>
                                            <option style="text-align:left;" value="Venezuela">Venezuela</option>
                                            <option style="text-align:left;" value="Vietnam">Vietnam</option>
                                            <option style="text-align:left;" value="Yemen">Yemen</option>
                                            <option style="text-align:left;" value="Zambia">Zambia</option>
                                            <option style="text-align:left;" value="Zimbabwe">Zimbabwe</option>  
                                        </select>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fifth">
                                <!-- Password -->
                                <td>
                                    <div>
                                        <label for="Password">
                                            Password:
                                        </label><br>
                                        <input id="Password" type="password" name="Password" placeholder="Password" required>
                                    </div>
                                </td>

                                <!-- ReconfirmPassword -->
                                <td>
                                    <div>
                                        <label for="ReconfirmPassword">
                                            Reconfirm Password:
                                        </label><br>
                                        <input id="ReconfirmPassword" type="password" name="ReconfirmPassword" placeholder="Reconfirm Password" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn sixth">
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Sign Up">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/Admin/manageClient.php">Back to Manage Client</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>