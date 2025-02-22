<?php
/**
 * Title: Filtros de albuns
 * Slug: filtros-albuns
 * Inserter: no
 */
?>

<form method="GET" action="" class="d-block d-100">
    <input type="hidden" name="post_type" value="albuns">
	
	<!-- Filtro de País -->
    <select name="pais" class="form-select form-select-sm d-inline-block w-auto me-2 my-1">
        <option value="">Todos os lugares</option> <!-- Default -->
        <option value="brasil" <?php selected($_GET['pais'] ?? '', 'brasil'); ?>>Brasil</option>
        <option value="bahia" <?php selected($_GET['pais'] ?? '', 'bahia'); ?>>Bahia</option>
    </select>

	<!-- Filtro de Ordenação -->
	<select name="ordenacao" class="form-select form-select-sm d-inline-block w-auto me-2 my-1">
		<option value="recentes" <?php selected($_GET['ordenacao'] ?? '', 'recentes'); ?>>Recém adicionados</option>
		<option value="antigos" <?php selected($_GET['ordenacao'] ?? '', 'antigos'); ?>>Mais antigos</option>
		<option value="lancamentos" <?php selected($_GET['ordenacao'] ?? '', 'lancamentos'); ?>>Lançamentos</option>
	</select>
	
	<!-- Filtro de Destaque -->
	<select name="destaque" class="form-select form-select-sm d-inline-block w-auto me-2 my-1">
		<option value="destaque" <?php selected($_GET['destaque'] ?? '', 'destaque'); ?>>Álbuns Destacados</option>
		<option value="classico" <?php selected($_GET['destaque'] ?? '', 'classico'); ?>>Álbuns Clássicos</option>
		<option value="todos" <?php selected($_GET['destaque'] ?? '', 'todos'); ?>>Todos os Álbuns</option>
	</select>
	
	<!-- Filtro de Tipo de Álbum -->
	<select name="tipo_album" class="form-select form-select-sm d-inline-block w-auto me-2 my-1">
		<option value="todos" <?php selected($_GET['tipo_album'] ?? '', 'todos'); ?>>Álbuns e Singles</option>
		<option value="album" <?php selected($_GET['tipo_album'] ?? '', 'album'); ?>>Somente Álbuns</option>
		<option value="single" <?php selected($_GET['tipo_album'] ?? '', 'single'); ?>>Somente Singles</option>
	</select>

    <button type="submit" class="btn btn-primary btn-sm my-1">Filtrar</button>
</form>
