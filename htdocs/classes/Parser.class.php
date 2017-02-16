<?php
require_once('Quran.class.php');
class Parser 
{
	//konstruktor
	public static function parse($text) {
		$quran = new Quran($text);
		if (strpos(strtolower($text), '!ayat') !== FALSE)  return $quran->cariAyat();
		else if (strpos(strtolower($text), '!cari_quran') !== false) return $quran->cariAlquran();
		else return $quran->geterrormsg(); //Nanti buat pesan kesalahan
	}	
}