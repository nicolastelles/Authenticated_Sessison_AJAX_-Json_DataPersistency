<?php 
	session_start();	
	date_default_timezone_set('America/Sao_Paulo');
	
  $json = file_get_contents('../data/data.json');
  $JsonData = json_decode($json,true);


	if ($_POST["operation"] == 'load') {
		
		if (isset($_SESSION["login"])) {
      echo json_encode($JsonData);

		} else {
			echo '{ "nome" : "undefined" }';
		}
		
	} else if ($_POST["operation"] == 'login') {
		if(!(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))){
        echo '{ "status" : "nao_logado" }';
		   header('HTTP/1.0 401 Unauthorized');
		} else {
			$login = $_SERVER['PHP_AUTH_USER'];
			$senha = $_SERVER['PHP_AUTH_PW'];
			
			if ($login == $JsonData["login"] &&
				$senha == $JsonData["senha"]) {
				$_SESSION["nome"] = $JsonData["nome"];
				$_SESSION["login"] = $JsonData["login"];
				
        echo json_encode($JsonData);

			} else {
        echo '{ "status" : "nao_logado" }';
				 header('HTTP/1.0 401 Unauthorized');
			 }
		}

	} else if ($_POST["operation"] == 'logout') {
		
		session_destroy();
		echo '{ "nome" : "undefined" }';
		
	}  else if ($_POST["operation"] == 'remoção') {

		if (isset($_SESSION["login"])) {
      if (isset($_POST["index"]) && $_POST["index"] != ""){
        
        $index = $_POST["index"];
        $JsonData["atividades"] = array_filter($JsonData["atividades"], function($atividade) use ($index) {
          return $atividade["index"] != $index;
        });
        $JsonData['atividades'] = array_values($JsonData['atividades']);
        if(file_put_contents('../data/data.json', json_encode($JsonData))){
          echo json_encode($JsonData);
        } else {
          echo '{ "status" : "ERRO AO SALVAR!" }';
        }

      } else {
        echo '{ "status" : "ERRO!" }';
      }

    } else {
      echo '{ "status" : "nao_logado" }';
      header('HTTP/1.0 401 Unauthorized');
    }

  } else if ($_POST["operation"] == 'Concluído') {

		if (isset($_SESSION["login"])) {
      if (isset($_POST["index"]) && $_POST["index"] != "") {
        
        $index = $_POST["index"];

        $JsonData["atividades"] = array_map(function($atividade) use ($index) {
          if ($atividade["index"] == $index) {
            $atividade["status"] = "done";
          }
          return $atividade;
        }, $JsonData["atividades"]);
        
    
        if(file_put_contents('../data/data.json', json_encode($JsonData))){
          echo json_encode($JsonData);
        } else {
          echo '{ "status" : "ERRO AO SALVAR!" }';
        }

      }else{
        echo '{ "status" : "ERRO AO CONCLUIR!" }';
      }

    }else{
      echo '{ "status" : "nao_logado" }';
      header('HTTP/1.0 401 Unauthorized');
    }
  
  }else if ($_POST["operation"] == 'Atualizar'){

		if (isset($_SESSION["login"])) {
      if (isset($_POST["index"]) && $_POST["index"] != "" && isset($_POST["atividade"]) && $_POST["atividade"] != "" ){
        $index = $_POST["index"];
        $atividadeUpdate = $_POST["atividade"];
      
        $JsonData["atividades"] = array_map(function($atividade) use ($index, $atividadeUpdate) {
          if ($atividade["index"] == $index) {
            $atividade["atividade"] = $atividadeUpdate;
            $atividade["date"] = date("d/m/Y").' - '. date("H:i:s");
          }
          return $atividade;
        }, $JsonData["atividades"]);

        if(file_put_contents('../data/data.json', json_encode($JsonData))){
          echo json_encode($JsonData);
        } else {
          echo '{ "status" : "ERRO AO SALVAR!" }';
        }

      } else {
        echo '{ "status" : "ERRO AO LOGAR!" }';
      }

    }else{
      echo '{ "status" : "nao_logado" }';
      header('HTTP/1.0 401 Unauthorized');
    }

  } else if ($_POST["operation"] == 'Inserir') {

		if (isset($_SESSION["login"])) {
      if (isset($_POST["atividade"]) && $_POST["atividade"] != ""){
        
        $atividade = $_POST["atividade"];

        $JsonData["atividades"][] = array(
          "index" => count($JsonData["atividades"]) + 1,
          "atividade" => $atividade,
          "date" => date("d/m/Y").' - '. date("H:i:s"),
          "status" => "open"
        );
          
        if(file_put_contents('../data/data.json', json_encode($JsonData))){
          echo json_encode($JsonData);
        } else {
          echo '{ "status" : "ERRO AO SALVAR!" }';
        }
      }else{
        echo '{ "status" : "ERRO AO INSERIR A ATIVIDADE" }';
      }

    }else{
      echo '{ "status" : "nao_logado" }';
      header('HTTP/1.0 401 Unauthorized');
    }

  } else {
		
		echo '{ "invalid_operation" : "' . $_POST["operation"] . '" }';
		
	}
?>