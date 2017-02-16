<?php

class Parser 
{
	private $text; //input dari user
	private $cari_alquran;
	private $base_url = 'http://api.alquran.cloud/';
	private $err_msg = "Masukkan Anda salah, Silahkan ketik !ayat Nomor/nama surat:ayat";
	private $nama_surat = array("fatihah", "baqarah", "imran", "nisa", "ma-idah", "an'am", "a'raf", "anfal", "taubah", "yunus",
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
	
	//konstruktor
	public function __construct($text) {
		$this->text = $text;
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

	private function getJson($url) {
		$json_raw = file_get_contents($url);
		return json_decode($json_raw);
	}

	private function cariAyat() {
		error_reporting(0);

		//hapus ' ' dan kata !ayat
		while ($dummy = strpos($this->text, ' ') !== FALSE) {
			$this->text = str_replace(' ', '', $this->text);
		}

		$this->text = str_replace('!ayat', '', strtolower($this->text));

		$jumlah_karakter = $this->isnomorsurataval();

		if ($jumlah_karakter !== TRUE) {
			$this->text = $this->carinomorsurat($jumlah_karakter);
			if ($this->text == FALSE) return $this->err_msg;
		}

		//Cari ayat dalam bahasa arab
		$json_array = $this->getJson($this->base_url.'ayah/'.$this->text.'/ar.asad');
		$result = '';

		foreach ($json_array as $datas => $data) {
			$result .= $data->text;
		}

		//Cari arti ayat yang diminta
		$json_array = $this->getJson($this->base_url.'ayah/'.$this->text.'/en.asad');

		$result .= "\r\n\r\n"; //tambahkan enter
		foreach ($json_array as $datas => $data) {
			$result .= $data->text;
		}

		utf8_decode($result);
		return $result;
	}

	private function cariAlquran() {
		$result = "anwar ramadha";
		utf8_encode($result);
		return result;
	}

	public function parser() {
		if (strpos(strtolower($this->text), '!ayat') !== FALSE)  return $this->cariAyat();
		else if (strpos(strtolower($this->text), '!surat') !== false || strpos($this->text, '!surat') != false) return $this->cariSurat();
		else if (strpos(strtolower($this->text), '!sari_quran') !== false || strpos($this->text, '!cari_quran') != false) return $this->cariAlquran();
		else return $this->err_msg; //Nanti buat pesan kesalahan
	}	
}