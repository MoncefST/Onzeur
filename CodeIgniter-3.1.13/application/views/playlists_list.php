<div class="container">
    <h1>Liste des Playlists</h1>
    <?php if ($this->session->flashdata('message')): ?>
        <div class="flash-message">
            <?php echo $this->session->flashdata('message'); ?>
        </div>
    <?php endif; ?>
    <a href="<?php echo site_url('playlists/create'); ?>" class="btn btn-primary">Créer une Nouvelle Playlist</a>
    <a href="<?php echo site_url('playlists/generate_random'); ?>" class="btn btn-primary">Générer une playlist aléatoire</a>
    <h1><br>Mes Playlists</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($playlists)) : ?>
                <?php foreach ($playlists as $playlist) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($playlist->name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($playlist->description, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="<?php echo site_url('playlists/view/' . $playlist->id); ?>" class="btn btn-info">Voir</a>
                            <a href="<?php echo site_url('playlists/delete/' . $playlist->id); ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette playlist ?');">Supprimer</a>
                            <a href="<?php echo site_url('playlists/duplicate/' . $playlist->id); ?>" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir dupliquer cette playlist ?');">Dupliquer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="3">Aucune playlist trouvée.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <h1><br>Playlists Publiques</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($public_playlists)) : ?>
                <?php foreach ($public_playlists as $playlist) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($playlist->name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($playlist->description, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="<?php echo site_url('playlists/view/' . $playlist->id); ?>" class="btn btn-info">Voir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="3">Aucune playlist publique trouvée.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
