<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Artiste extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Model_artist');
        $this->load->model('Model_music');
        $this->load->helper('url');
        $this->load->helper('html');
    }

    public function index($artiste_id){
        // Récupérer les détails de l'artiste
        $artiste = $this->Model_artist->getArtisteById($artiste_id);
        $mostUsedGenre = $this->Model_music->getMostUsedGenreByArtist($artiste_id);
    
        if($artiste){
            // Récupérer tous les albums de l'artiste
            $albums = $this->Model_music->getAlbumsByArtiste($artiste_id);
            
            // Charger la vue avec les détails de l'artiste et ses albums
            $data['artiste'] = $artiste;
            $data['albums'] = $albums;
            $data['mostUsedGenre'] = $mostUsedGenre; // Passer $mostUsedGenre à la vue
            $this->load->view('layout/header_not_logged_dark');
            $this->load->view('artiste_details', $data);
            $this->load->view('layout/footer_dark');
        } else {
            // Gérer le cas où l'artiste n'est pas trouvé == afficher un error 404
            show_404();
        }
    }
    

    public function list_artists(){
        // Récupérer le paramètre de tri (croissant ou decroissant)
        $order = $this->input->get('order') ? $this->input->get('order') : 'ASC';

        // Récupérer la liste des artistes
        $artists = $this->Model_artist->getArtists($order);

        // Charger la vue avec la liste des artistes
        $data['artists'] = $artists;
        $data['current_order'] = $order;
        $this->load->view('layout/header_not_logged_dark');
        $this->load->view('artists_list', $data);
        $this->load->view('layout/footer_dark');
    }
}
?>
