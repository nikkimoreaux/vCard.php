# A very simple PHP vCard generator
**With photo support.**

Limitation: one phone/mail/address/url max.

Below, a complete implementation:

	header("Content-type: text/x-vcard");
	header("Content-Disposition: attachment; filename=\"john_doe.vcf\";");
	
	require_once('vCard.php');

	$vcard = new vCard;
	
	$vcard->setName("John","Doe");

	// Every set functions below are optional
	$vcard->setTitle("Software dev.");
	$vcard->setPhone("+1234567890");
	$vcard->setURL("http://johndoe.com");
	$vcard->setTwitter("diplodocus");
	$vcard->setMail("john@johndoe.com");
	$vcard->setAddress(array(
		"street_address"   => "Main Street",
		"city"             => "Ghost Town",
		"state"            => "",
		"postal_code"      => "012345",
		"country_name"     => "Somewhere"
	));
	$vcard->setNote("Lorem Ipsum, \nWith new line.");
	$vcard->setPhoto("john.jpg"); 
	
	echo $vcard;
	


##License

This project is released under the [MIT License](http://www.opensource.org/licenses/mit-license.php).
