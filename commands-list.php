<?php
add_action( 'init', function(  ) {

	$commands_manifest = 'https://raw.githubusercontent.com/wp-cli/handbook/master/bin/commands-manifest.json';

	$response = wp_remote_get( $commands_manifest );

	if ( is_wp_error( $response ) ) {
		vl( $response );
	} elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
		vl( new WP_Error( 'invalid-http-code', 'Markdown source returned non-200 http code.' ) );
	}

	$manifest = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! $manifest ) {
		vl( new WP_Error( 'invalid-manifest', 'Manifest did not unfurl properly.' ) );
	}

	// vl($manifest);
	$docs = array();

	foreach ($manifest as $key => $value)
	{
		if( $value['parent'] && strpos($key, '/') !== false )
		{
			// vl( '-------------------------------' );
			// vl($value) . '<br/>';
			// echo $value['cmd_path'] . ' | ';
			// echo '<a href="'.$value['markdown_source'].'">Documentation</a> | ';
			// echo '<a href="'.$value['repo_url'].'">Repository</a></br>';

			// vl( $value );

			if ( strpos($value['parent'], '/') === false) {
				$temp_key = $value['title'];
				$docs[ $value['parent'] ][ $temp_key ] = $value;
			} else {
				$dat = explode('/', $value['parent']);
				$temp_key = str_replace('-', ' ', $value['title']);
				$docs[ $dat[0] ][ $temp_key ] = $value;
			}

		} else {
			// $docs[ $value['parent'] ] = $value;
		}

		// if ( strpos($key, '/') !== false) {
		// 	// vl( $key );
		// 	// vl( $value );

		// 	$doc_url  = '<a href="'.$value['markdown_source'].'">Documentation</a>';
		// 	$repo_url = '<a href="'.$value['repo_url'].'">Repository</a>';
		// 	// echo '<tr><td>wp ' . $value['parent'] . ' ' . $value['title'] . '</td><td>'.$doc_url.' | '.$repo_url.'<td>';
		// }
		// echo $value['title'] . '<br/>';
	}	
	// vl( $docs );
	

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=wp-cli-commands-list.html ");
	header("Content-Transfer-Encoding: binary ");

	$data = '<table>';
	foreach ($docs as $comment => $subcommands)
	{
		$data .= '<tr><th colspan="2">'.ucwords($comment).'</th></tr>';

		$i = 1;
		foreach ($subcommands as $k => $v) {
			$data .= '<tr>';
			// $data .= '<td>';
			// $data .= $i;
			// $data .= '</td>';
			$data .= '<td style="padding: 0px 8px;">';
			$data .= 'wp ' . $v['title'];
			$data .= '</td>';
			$data .= '<td style="padding: 0px 8px;">';
			$data .= '<a style="box-shadow: none;" href="'.$value['markdown_source'].'">Docs</a>';
			$data .= ' | ';
			$data .= '<a style="box-shadow: none;" href="'.$value['repo_url'].'">Git</a>';
			$data .= '</td>';
			$data .= '</tr>';
			
			$i++;
		}
	}
	$data .= '</table>';

	echo $data;
	exit();
} );
