<?php
    permission_check("advertise");
    echo "<h2>Advertise Item:</h2>";
    echo "<hr>";
	if (isset($_POST['register'])){
		// create database values to store in the database
		foreach ($_POST as $key => $value){
		    switch ($key){
		        case "register":
		            break;
		        case "fileToUpload":
		            break;
		        case "collection":
		            if (strtolower($value)=="on"){
		                $value = 1;
		            }else{
		                $value = 0;
		            }
		            $database_values[$key] = $value;
		        default:
    				$database_values[$key] = $value;		            
		    }
		}
		$database_values['userID'] = $uid;
		//moderators are supposed to turn the active to true, but for testing purposes it's already turned here instead
		$database_values['available'] = 1;
		$database_values['timestamp'] = time();				// timestamp of registration
		$saved = $db->insert("fh_adverts", $database_values);	
		if ($saved){
			//forward to relevant confirmation page
			echo ("Saved");
			// upload the image			
			$target_dir = "./advert_images/";
			$fileName = basename($_FILES["fileToUpload"]["name"]);
			$encryption->setPlainText($fileName . $uid . time());
			$serverName = $encryption->classRun();
			$fileChk = $target_dir . $fileName;
			$target_file = $target_dir . $serverName;
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($fileChk,PATHINFO_EXTENSION));

			// Check if image file is a actual image or fake image
			$error = "";
			if (isset($_FILES["error"])){
				if ($_FILES["error"] == 0){
				    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
				
				    if($check !== false) {
					    $uploadOk = 1;
				    } else {
					    $error .= "File is not an image.<br>";
					    $uploadOk = 0;
				    }
			  
    				
    				// Check if file already exists
    				if (file_exists($target_file)) {
    					$error .= "Sorry, file already exists.<br>";
    					$uploadOk = 0;
    				}
    				
    				// Check file size
    				if ($_FILES["fileToUpload"]["size"] > 500000) {
    					$error .= "Sorry, your file is too large.<br>";
    					$uploadOk = 0;
    				}

    				// Allow certain file formats
    				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    				&& $imageFileType != "gif" && $imageFileType != "webp" ) {
    					$error .= "Sorry, only JPG, JPEG, PNG, WEBP & GIF files are allowed.<br>";
    					$uploadOk = 0;
    				}
				}else{
				    $error .= "Default image will be used";
				    $uploadOk = 1;
				}
			}

			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
			} else {
			    if ($_FILES["fileToUpload"]["error"] == 0){
    				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    					echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
    					$advID = $db->query("fh_adverts", null, "userID='" . $uid . "' and name='" . $_POST['name'] . "' AND active=0", false, ['timestamp'=>'DESC'], 1);
    					$data['advert_id'] = $advID[0]['id'];
    					$data['file_name'] = $serverName;
    					$data['active'] = 0;
    					$data['timestamp'] = time();
    					$data['ip'] = $_SERVER['REMOTE_ADDR'];
    					$data['imageNo'] = 1;
    					$saved = $db->insert("fh_advert_images", $data);
    					if ($saved){
    						echo "file saved";
    					}
    				} else {
					    echo "Sorry, there was an error uploading your file.";
				    }
			    }
			}
		}
		else{
			echo ("Unable to register, try again or speak with administration");
		}
	}
?>
<script>
function showPreview(event){
  if(event.target.files.length > 0){
    var src = URL.createObjectURL(event.target.files[0]);
    var preview = document.getElementById("ad-preview");
    preview.src = src;
    preview.style.display = "block";
  }
}

function updatePreviewText(elID, item){
    let el = document.getElementById(elID);
    el.innerHTML = item.value;
}

function hideAndShowPreviewLabel(elID, item){
    let el = document.getElementById(elID);
    if (item.checked){
        el.innerHTML = "<h3>This item is for collection only</h3>Collection information will be available once item has been booked";
    }else{
        el.innerHTML = "";
    }
}


</script>

<?php
    echo "<div class='contactContainer column'>";
    echo "<h3 class='subheading'>Advert Editor</h3>";
	echo "<table>";
	echo "<tr>";
	echo "<td>";
	echo "<form method='post' action='?page=advertise' name='submit_form' id='register_form' class='form' enctype='multipart/form-data'>";
	echo "<p class='form_p'>Title:</p>";
	echo "<input type='text' onchange='updatePreviewText(\"ad-name\", this);' id='name' name='name'" . (isset($_POST["name"])? " value = '" . $_POST["name"] . "'" : '') . " placeholder='Title' class='form_txtBox'>";
	echo "<p class='form_p'>Description:</p>";
	echo "<textarea onchange='updatePreviewText(\"ad-description\", this);' name='description' placeholder='Advert' class='form_txtBox'>" . (isset($_POST["description"])? " value = '" . $_POST["description"] . "'" : ""). "</textarea>";
	echo "<p class='form_p'>Items Available:</p>";
	echo "<input type = 'number' onchange='updatePreviewText(\"ad-avail\", this);' name='amount_available' id = 'no_of_items'" . (isset($_POST["amount_available"])? " value = '" . $_POST["amount_available"] . "'" : '') . " placeholder = 'Number of items' required min = '1' class='form_txtBox'>";
	echo "<p class='form_p'>Cost per day:</p>";
	echo "<input type = 'number' onchange='updatePreviewText(\"ad-cost\", this);' name='credits' id = 'no_of_credits'" . (isset($_POST["credits"])? " value = '" . $_POST["credits"] . "'" : "") . " placeholder = 'Number of credits' required min = '1' class='form_txtBox'>";
	echo  "<p>Collection Only</p>";
	echo "<input type='checkbox'  onchange='hideAndShowPreviewLabel(\"collectionMsg\", this);' name='collection' class='checkboxes'>";
    echo "<p class='form_p'>Image:</p>";
    echo "<input type='file'  class='btn' name='fileToUpload' id='fileToUpload' accept='image/*' onchange='showPreview(event);'>";
	echo "<input type='submit' class='btn' name='register' class='form_button' id='reg_button' value='Create Advert'>";
	echo "<input type='reset'  class='btn' name='clear' class='form_button' id='clear_button' value='Clear'>";
	echo "</form>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<div class='contactContainer column' id='adPreviewWindow'>";
	echo "<h3 class='subheading'>Preview Window</h3>";
        echo "<table>";
        echo "<tr>";
        echo "<td rowspan=10 class='adImageCell'>";
        echo "<img id='ad-preview' src='" . $img_dir . "noimage.jpg' alt='frackhub image' class='advert_image_small'>";
        echo "</td>";
        echo "<td><h3 id='ad-name'></h3></td>";
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td><h4>Description</h4></td>";
        echo "</tr><tr>";
        echo "<td id='ad-description'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td><h4>Available</h4></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td id='ad-avail'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td><h4>Area</h4></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td >" . $user['address_line3'] . "</td>";
        echo "</tr>";
        echo "<td><h4>Cost per day:</h4></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td id='ad-cost'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td id='collectionMsg'></td>";
        echo "<tr>";
        echo "</table>";	
	echo "</td>";
	echo "</table>";
	echo "</div>";
?>
