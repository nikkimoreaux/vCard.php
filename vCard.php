<?php

/*

Copyright © 2013 Nikki Moreaux http://diplodoc.us
 
Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:
 
The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.
 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

*/

class vCard
{
	const version = "3.0";
	const endl = "\n";
	
	private $n = false;
	private $url = false;
	private $adr = false;
	private $tel = false;
	private $title = false;
	private $email = false;
	private $twitter = false;
	private $note = false;
	private $photo = false;
	private $address = array(
		"post_office_box_sould_not_be_set"  => "",
		"extended_address_sould_not_be_set" => "",
		"street_address"   => "",
		"city"             => "",
		"state"            => "",
		"postal_code"      => "",
		"country_name"     => ""
	);
	
	
	function setName($first_name,$last_name){
		$this->n = array($first_name,$last_name);
	}
	function setPhone($tel){
		$this->tel = $tel;
	}
	function setURL($url){
		$this->url = $url;
	}
	function setTitle($title){
		$this->title = $title;
	}
	function setMail($email){
		$this->email = $email;
	}
	function setTwitter($twitter){
		$this->twitter = $twitter;
	}
	function setNote($note){
		$this->note = $note;
	}
	// Merge new address array into default address array, skip other keys
	function setAddress(array $address){
		foreach($this->address as $key => &$value){
			if(array_key_exists($key,$address)){
				$value = $address[$key];
			}
		}
	}
	
	// vCard.php automatically square crop + compress photo.
	function setPhoto($photo_path){
		$im = @imagecreatefromstring(file_get_contents($photo_path));
				
		if($im){
			$im_x = imagesx($im);
			$im_y = imagesy($im);
			$im_xy_min = min($im_y,$im_x);
			
			$im_cropped_xy = min(400,$im_xy_min);
			$im_cropped = imagecreatetruecolor($im_cropped_xy,$im_cropped_xy);
						
			imagecopyresampled(
				$im_cropped,$im,0,0,
				floor(($im_x-$im_xy_min)/2),floor(($im_y-$im_xy_min)/2),
				$im_cropped_xy,$im_cropped_xy,$im_xy_min,$im_xy_min);
			
			
			ob_start();
			imagejpeg($im_cropped);
			$this->photo = base64_encode(ob_get_contents());
			ob_end_clean();
			
			imagedestroy($im);
			imagedestroy($im_cropped);
		}
	}
	
	
	function __toString(){
		if(!$this->n){
			trigger_error("You must at least use setName",E_USER_ERROR);
		}else{
			$vcard_text = "BEGIN:VCARD".self::endl;
			$vcard_text .= "VERSION:". self::version .self::endl;
			
			// Return name
			$vcard_text .= "N:". self::escapeVCard($this->n[1]) .";". self::escapeVCard($this->n[0]) .";;;" .self::endl;
			$vcard_text .= "FN:". self::escapeVCard($this->n[0]) ." ". self::escapeVCard($this->n[1]) .self::endl;
			
			// Return tel, email, url, job title, twitter if they was set...
			if($this->tel){
				$vcard_text .= "TEL:". self::escapeVCard($this->tel) .self::endl;
			}
			if($this->email){
				$vcard_text .= "EMAIL:". self::escapeVCard($this->email) .self::endl;
			}
			if($this->url){
				$vcard_text .= "URL:". self::escapeVCard($this->url) .self::endl;
			}
			if($this->title){
				$vcard_text .= "TITLE:". self::escapeVCard($this->title) .self::endl;
			}
			if($this->twitter){
				$vcard_text .= "X-SOCIALPROFILE;type=twitter:http\\://twitter.com/". self::escapeVCard($this->twitter) .self::endl;
			}
			if($this->note){
				$vcard_text .= "NOTE:". self::escapeVCard($this->note) .self::endl;
			}
			
			// Return address if it was set
			if(strlen(implode("",$this->address))>0){
				$adr_text = false;
				foreach($this->address as $value){
					if(!$adr_text){
						$adr_text = "ADR:". self::escapeVCard($value);
					}else{
						$adr_text .= ";". self::escapeVCard($value);
					}
				}
				$vcard_text .= $adr_text.self::endl;
			}
			
			// Return photo Base64 if it was set
			if($this->photo){
				$vcard_text .= "PHOTO;ENCODING=b;TYPE=JPEG:". self::escapeVCard($this->photo) .self::endl;
			}
			
			$vcard_text .= 'END:VCARD'.self::endl;
			
			return $vcard_text;
		}
	}
	
	
	private static function escapeVCard($string){
		return str_replace(array(':',';',',',"\n"),array('\:','\;','\,','\n'),$string);
	}
}





?>