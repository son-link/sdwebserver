<h3>User data</h3>
<div id="user-edit-form">
	<form method="post" enctype="multipart/form-data">
		<div class="row">
			<div class="col-2">
				<label>Username:</label>
			</div>
			<div class="col-6">
				<input type="text" value="<?=$user->username?>" disabled />
			</div>
		</div>
		<div class="row">
			<div class="col-2">
				<label>Email:</label>
			</div>
			<div class="col-6">
				<input type="email" id="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" value="<?=$user->email?>" />
				<span class="input-error">Email address isn't valid</span>
			</div>
		</div>
		<div class="row">
			<div class="col-2">
				<label>Nation:</label>
			</div>
			<div class="col-6">
				<select name="nation" id="flaginput" value="<?=$user->nation?>">
					<option>Afghanistan</option>
					<option>Albania</option>
					<option>Algeria</option>
					<option>American Samoa</option>
					<option>Andorra</option>
					<option>Angola</option>
					<option>Anguilla</option>
					<option>Antigua and Barbuda</option>
					<option>Argentina</option>
					<option>Armenia</option>
					<option>Aruba</option>
					<option>Australia</option>
					<option>Austria</option>
					<option>Azerbaijan</option>
					<option>Bahamas</option>
					<option>Bahrain</option>
					<option>Bangladesh</option>
					<option>Barbados</option>
					<option>Belarus</option>
					<option>Belgium</option>
					<option>Belize</option>
					<option>Benin</option>
					<option>Bermuda</option>
					<option>Bhutan</option>
					<option>Bolivia</option>
					<option>Bosnia</option>
					<option>Botswana</option>
					<option>Brazil</option>
					<option>British Virgin Islands</option>
					<option>Brunei</option>
					<option>Bulgaria</option>
					<option>Burkina Faso</option>
					<option>Burundi</option>
					<option>Cambodia</option>
					<option>Cameroon</option>
					<option>Canada</option>
					<option>Cape Verde</option>
					<option>Cayman Islands</option>
					<option>Central African Republic</option>
					<option>Chad</option>
					<option>Chile</option>
					<option>China</option>
					<option>Christmas Island</option>
					<option>Colombia</option>
					<option>Comoros</option>
					<option>Cook Islands</option>
					<option>Costa Rica</option>
					<option>Croatia</option>
					<option>Cuba</option>
					<option>Cyprus</option>
					<option>Czech Republic</option>
					<option>Côte d'Ivoire</option>
					<option>Democratic Republic of the Congo</option>
					<option>Denmark</option>
					<option>Djibouti</option>
					<option>Dominica</option>
					<option>Dominican Republic</option>
					<option>Ecuador</option>
					<option>Egypt</option>
					<option>El Salvador</option>
					<option>Equatorial Guinea</option>
					<option>Eritrea</option>
					<option>Estonia</option>
					<option>Ethiopia</option>
					<option>Falkland Islands</option>
					<option>Faroe Islands</option>
					<option>Fiji</option>
					<option>Finland</option>
					<option>France</option>
					<option>French Polynesia</option>
					<option>Gabon</option>
					<option>Gambia</option>
					<option>Georgia</option>
					<option>Germany</option>
					<option>Ghana</option>
					<option>Gibraltar</option>
					<option>Greece</option>
					<option>Greenland</option>
					<option>Grenada</option>
					<option>Guam</option>
					<option>Guatemala</option>
					<option>Guinea</option>
					<option>Guinea Bissau</option>
					<option>Guyana</option>
					<option>Haiti</option>
					<option>Honduras</option>
					<option>Hong Kong</option>
					<option>Hungary</option>
					<option>Iceland</option>
					<option>India</option>
					<option>Indonesia</option>
					<option>Iran</option>
					<option>Iraq</option>
					<option>Ireland</option>
					<option>Israel</option>
					<option>Italy</option>
					<option>Jamaica</option>
					<option>Japan</option>
					<option>Jordan</option>
					<option>Kazakhstan</option>
					<option>Kenya</option>
					<option>Kiribati</option>
					<option>Kuwait</option>
					<option>Kyrgyzstan</option>
					<option>Laos</option>
					<option>Latvia</option>
					<option>Lebanon</option>
					<option>Lesotho</option>
					<option>Liberia</option>
					<option>Libya</option>
					<option>Liechtenstein</option>
					<option>Lithuania</option>
					<option>Luxembourg</option>
					<option>Macao</option>
					<option>Macedonia</option>
					<option>Madagascar</option>
					<option>Malawi</option>
					<option>Malaysia</option>
					<option>Maldives</option>
					<option>Mali</option>
					<option>Malta</option>
					<option>Marshall Islands</option>
					<option>Martinique</option>
					<option>Mauritania</option>
					<option>Mauritius</option>
					<option>Mexico</option>
					<option>Micronesia</option>
					<option>Moldova</option>
					<option>Monaco</option>
					<option>Mongolia</option>
					<option>Montserrat</option>
					<option>Morocco</option>
					<option>Mozambique</option>
					<option>Myanmar</option>
					<option>Namibia</option>
					<option>Nauru</option>
					<option>Nepal</option>
					<option>Netherlands</option>
					<option>Netherlands Antilles</option>
					<option>New Zealand</option>
					<option>Nicaragua</option>
					<option>Niger</option>
					<option>Nigeria</option>
					<option>Niue</option>
					<option>Norfolk Island</option>
					<option>North Korea</option>
					<option>Norway</option>
					<option>Oman</option>
					<option>Pakistan</option>
					<option>Palau</option>
					<option>Panama</option>
					<option>Papua New Guinea</option>
					<option>Paraguay</option>
					<option>Peru</option>
					<option>Philippines</option>
					<option>Pitcairn Islands</option>
					<option>Poland</option>
					<option>Portugal</option>
					<option>Puerto Rico</option>
					<option>Qatar</option>
					<option>Republic of the Congo</option>
					<option>Romania</option>
					<option>Russian Federation</option>
					<option>Rwanda</option>
					<option>Saint Kitts and Nevis</option>
					<option>Saint Lucia</option>
					<option>Saint Pierre</option>
					<option>Saint Vicent and the Grenadines</option>
					<option>Samoa</option>
					<option>San Marino</option>
					<option>Sao Tomé and Príncipe</option>
					<option>Saudi Arabia</option>
					<option>Senegal</option>
					<option>Serbia and Montenegro</option>
					<option>Seychelles</option>
					<option>Sierra Leone</option>
					<option>Singapore</option>
					<option>Slovakia</option>
					<option>Slovenia</option>
					<option>Soloman Islands</option>
					<option>Somalia</option>
					<option>South Africa</option>
					<option>South Georgia</option>
					<option>South Korea</option>
					<option>Soviet Union</option>
					<option>Spain</option>
					<option>Sri Lanka</option>
					<option>Sudan</option>
					<option>Suriname</option>
					<option>Swaziland</option>
					<option>Sweden</option>
					<option>Switzerland</option>
					<option>Syria</option>
					<option>Taiwan</option>
					<option>Tajikistan</option>
					<option>Tanzania</option>
					<option>Thailand</option>
					<option>Tibet</option>
					<option>Timor-Leste</option>
					<option>Togo</option>
					<option>Tonga</option>
					<option>Trinidad and Tobago</option>
					<option>Tunisia</option>
					<option>Turkey</option>
					<option>Turkmenistan</option>
					<option>Turks and Caicos Islands</option>
					<option>Tuvalu</option>
					<option>UAE</option>
					<option>Uganda</option>
					<option>Ukraine</option>
					<option>United Kingdom</option>
					<option>United States of America</option>
					<option>Uruguay</option>
					<option>US Virgin Islands</option>
					<option>Uzbekistan</option>
					<option>Vanuatu</option>
					<option>Vatican City</option>
					<option>Venezuela</option>
					<option>Vietnam</option>
					<option>Wallis and Futuna</option>
					<option>Yemen</option>
					<option>Zambia</option>
					<option>Zimbabwe</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-2">
				<label>Profile image:</label>
			</div>
			<div class="col-6">
				<input type="file" name="imginput" id="imginput" />
			</div>
			<div class="col-12">
				<img src="<?= base_url() . "/img/users/{$user->img}" ?>" id="profile-img">
			</div>
		</div>
		<div class="register-right">
			<input type="submit" value="Update" class="primary" />
		</div>
	</form>
</div>

<h3>Password</h3>
<div id="user-passwd-form">
	<form method="post" enctype="multipart/form-data" action="/dashboard/change_passwd">
		<label>Current password:</label>
		<input type="password" name="cur_password" id="cur_password" required />
		<label>Password:</label>
		<input type="password" name="password" id="password" required />
		<label>Repeat password:</label>
		<input type="password" name="passwordcheck" id="passwordcheck" required />
		<span class="input-error" id="passwd-error">Passwords don't match</span>
		<div class="register-right">
			<input type="submit" value="Change" class="primary" />
		</div>
	</form>
</div>

<script>
	const nation = '<?=$user->nation?>'
</script>