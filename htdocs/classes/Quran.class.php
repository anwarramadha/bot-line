<?php
require 'Client.class.php';

class Quran 
{
	private $text; //input dari user
	private $err_input = "Masukkan Anda salah, Silahkan ketik !ayat Nomor/nama surat:ayat";
	private $err_result = "Maaf data yang anda masukkan tidak ada di database kami";
	private $nama_surat = array("fatihah", "baqarah", "imran", "nisa", "maidah", "an'am", "a'raf", "anfal", "taubah", "yunus",
								"hud", "yusuf", "ra'd", "ibrahim", "hijr", "nahl", "isra", "kahf", "maryam", "tha ha",
								"anbiya", "hajj", "mu'minun", "nur", "furqan", "syu'ara'", "naml", "qashash", "ankabut", "rum",
								"luqman", "sajdah", "ahzab", "saba'", "fathir", "ya sin", "ash-shaffat", "shad", "zumar", "ghafir",
								"fushshilat", "asy-syura", "zukhruf", "dukhan", "jatsiyah", "ahqaf", "muhammad", "fath", "hujurat", "qaf",
								"dzariyat", "thur", "najm", "qamar", "rahman", "waqi'ah", "hadid", "mujadilah", "hasyr", "mumtahanah",
								"shaff", "jumu'ah", "munafiqun", "taghabun", "thalaq", "tahrim", "mulk", "qalam", "haqqah", "ma'arij",
								"nuh", "jinn", "muzzammil", "muddatstsir", "qiyamah", "insan", "mursalat", "naba'", "nazi'at", "'abasa",
								"takwir", "infithar","muthaffifin", "insyiqaq", "buruj","thariq", "a'la", "ghasyiyah", "fajr", "balad", 
								"syams", "lail", "dluha", "syarh", "tin", "'alaq", "qadr", "bayyinah", "zalzalah", "'adiyat", 
								"qari'ah", "takatsur", "ashr", "humazah", "fil", "quraisy", "ma'un", "kautsar", "kafirun", "nashr", 
								"lahab", "ikhlas", "falaq", "nas");
	
	
	public function __construct($text) {
		error_reporting(0);
		$this->text = $text;
	}

	public function geterrormsg() {
		return $this->err_input;
	}
	//cek apakah nomor surat ditulis secara eksplisit
	private function isnomorsurataval() {
		$jumlah_digit = strpos($this->text, ':');
		$nomor_ayat = substr($this->text, 0, $jumlah_digit);
		if (is_numeric($nomor_ayat)) return TRUE;
		return $jumlah_digit;
	}

	//cari nomor surat berdasarkan nama surat
	private function carinomorsurat($jumlah_karakter) {
		$indeks_surat = 0;

		while ($indeks_surat < 114) {
			if (strpos(strtolower($this->text), $this->nama_surat[$indeks_surat]) !== FALSE){ 
				return substr_replace($this->text, $indeks_surat+1, 0, $jumlah_karakter); //ubah nama surat menjadi nomor surat
			} //cari nama surat kemudian kembalikan nomor surat
			$indeks_surat++;
		}
		return FALSE;
	}

	public function cariAyat() {
		//buat objek client api
		$client = new Client; 

		//hapus ' ' dan kata !ayat
		while ($dummy = strpos($this->text, ' ') !== FALSE) {
			$this->text = str_replace(' ', '', $this->text);
		}

		$this->text = str_replace('!ayat', '', strtolower($this->text));

		$jumlah_karakter = $this->isnomorsurataval();

		if ($jumlah_karakter !== TRUE) {
			$this->text = $this->carinomorsurat($jumlah_karakter);
			if ($this->text == FALSE) return $this->err_input;
		}

		$result = '';
		//Cari ayat dalam bahasa arab
		$json_array = $client->ayah($this->text);
		$result =  $json_array -> data -> text;

		//Cari arti ayat yang diminta
		$json_array = $client->ayah($this->text, 'id.indonesian');

		$result .= "\r\n\r\n"; //tambahkan enter
		$result .= $json_array -> data -> text;

		utf8_decode($result);
		unset($client);
		if ($result!='') return $result;
		return $this->err_result;
	}

	public function cariAlquran() {
		//buat objek alquran cloud client api
		$client = new Client();

		$this->text = str_replace('!cariquran', '', strtolower($this->text));
		$this->text = urlencode($this->text);
		$json_array = $client->search($this->text, null, 'id.indonesian');
		// echo $this->base_url.'search/'.$this->text.'/all/id';
		$result = '';
		$jumlah_ayat = 0;
		foreach($json_array -> data as $data) {
			foreach ($data as $matches => $match) {
				if ($jumlah_ayat == 0) {
					$result .= $match->text."\r\n\r\nBeberapa referensi lain:\r\n";
				}
				else {
					$surah_name = $match->surah->name;
					utf8_decode($surah_name);
					$result .= $surah_name.':'.$match->numberInSurah."\r\n";
				}

				if ($jumlah_ayat > 5) break;
				$jumlah_ayat ++;
			}
		}
		unset($client);
		if ($result!='') return $result;
		return $this->err_result;
	}
}