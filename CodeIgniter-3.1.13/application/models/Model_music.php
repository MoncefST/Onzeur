<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_music extends CI_Model {
    public function __construct(){
        $this->load->database();
    }

    public function getAlbums($limit, $offset, $order_by = 'year', $genre_id = null, $artist_id = null){
        $sql = "SELECT album.id, album.name, album.year, artist.name as artistName, genre.name as genreName, cover.jpeg
                FROM album
                JOIN artist ON album.artistid = artist.id
                JOIN genre ON album.genreid = genre.id
                JOIN cover ON album.coverid = cover.id";
        
        if ($genre_id) {
            $sql .= " WHERE genre.id = ?";
            $params[] = $genre_id;
        }
        
        if ($artist_id) {
            if ($genre_id) {
                $sql .= " AND artist.id = ?";
            } else {
                $sql .= " WHERE artist.id = ?";
            }
            $params[] = $artist_id;
        }

        $sql .= " ORDER BY album." . $order_by . " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $query = $this->db->query($sql, $params);
        return $query->result();
    }

    public function get_total_albums($genre_id = null, $artist_id = null){
        $sql = "SELECT COUNT(*) as total_albums FROM album
                JOIN genre ON album.genreid = genre.id
                JOIN artist ON album.artistid = artist.id";
        
        $params = array(); // Initialiser le tableau de paramètres
    
        if ($genre_id) {
            $sql .= " WHERE genre.id = ?";
            $params[] = $genre_id;
        }
        
        if ($artist_id) {
            if ($genre_id) {
                $sql .= " AND artist.id = ?";
            } else {
                $sql .= " WHERE artist.id = ?";
            }
            $params[] = $artist_id;
        }
    
        $query = $this->db->query($sql, $params);
        $result = $query->row();
        return $result->total_albums;
    }
    

    // Méthodes pour obtenir les genres et les artistes pour les filtres
    public function getGenres(){
        $query = $this->db->query("SELECT id, name FROM genre ORDER BY name");
        return $query->result();
    }

    public function getArtists(){
        $query = $this->db->query("SELECT id, name FROM artist ORDER BY name");
        return $query->result();
    }

    public function get_album_by_id($id){
        $query = $this->db->query(
            "SELECT album.id, album.name, album.year, artist.name as artistName, genre.name as genreName, cover.jpeg
             FROM album
             JOIN artist ON album.artistid = artist.id
             JOIN genre ON album.genreid = genre.id
             JOIN cover ON album.coverid = cover.id
             WHERE album.id = ?", array($id)
        );
        $album = $query->row();
    
        if ($album) {
            $query = $this->db->query(
                "SELECT track.id, track.diskNumber, track.number, track.duration, song.name as songName
                 FROM track
                 JOIN song ON track.songid = song.id
                 WHERE track.albumid = ?
                 ORDER BY track.diskNumber, track.number", array($id)
            );
            $album->tracks = $query->result();
        }
    
        return $album;
    }

    public function getMusiques($limit, $offset) {
        $query = $this->db->query(
            "SELECT song.id, song.name, artist.id as artist_id, artist.name as artistName, album.name as album_name, track.albumid as album_id, cover.jpeg as cover
             FROM song
             JOIN track ON song.id = track.songid
             JOIN album ON track.albumid = album.id
             JOIN artist ON album.artistid = artist.id
             JOIN cover ON album.coverid = cover.id
             LIMIT $limit OFFSET $offset"
        );        
        return $query->result();
    }

    
    public function get_total_musiques(){
        $query = $this->db->query("SELECT COUNT(*) as total_musiques FROM song");
        $result = $query->row();
        return $result->total_musiques;
    } 

    public function getAlbumsByArtiste($artiste_id){
        $query = $this->db->query("
            SELECT album.id, album.name, album.year, artist.name as artistName, genre.name as genreName, cover.jpeg
            FROM album
            JOIN artist ON album.artistid = artist.id
            JOIN genre ON album.genreid = genre.id
            JOIN cover ON album.coverid = cover.id
            WHERE artist.id = ?
        ", array($artiste_id));
        return $query->result();
    }
}
?>
