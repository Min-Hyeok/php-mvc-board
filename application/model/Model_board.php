<?php
	Class Model_board extends Model{

		//view 페이지 정보,파일 정보
		function getView(){
			if ($this->param->page == "view") {//조회수 증가
				$this->query("UPDATE board SET hit=hit+1 where idx='{$this->param->idx}'");				
			}
			return $this->fetch("SELECT * FROM board where idx='{$this->param->idx}'");
		}

		function prev(){
			$prev = (array)$this->fetch("SELECT max(idx) FROM board where idx < '{$this->param->idx}'");
			return $prev['max(idx)'];
		}

		function next(){
			$next = (array)$this->fetch("SELECT min(idx) FROM board where idx > '{$this->param->idx}'");
			return $next['min(idx)'];
		}

		function delete(){
			$data = $this->getView();
			access($data->midx != $_SESSION['member']->idx,"작성자만 접근할 수 있습니다.");
			@unlink(_DATA.$data->change_name);
			$this->query("DELETE FROM board where idx='{$this->param->idx}'");
		}

		function process(){
			$this->action = $_POST['action'];
			$this->table = "board";
			$cancel = "/action/file";
			$column = $this->get_column($_POST,$cancel);
			$add_sql = "";
			$change_name = "";
			$data = $this->getView();
			switch ($this->action) {
				case 'insert':
					if (isset($_FILES['file']) && $_FILES['file']['tmp_name'] != "") {
						$change_name = file_upload($_FILES['file']);
						$add_sql .= ", file_name='{$_FILES['file']['name']}'";
						$add_sql .= ", file_size='{$_FILES['file']['size']}'";
						$add_sql .= ", change_name='{$change_name}'";
					}
					$add_sql .= ", date=now()";
				break;
				case 'update':
					if (isset($_FILES['file']) && $_FILES['file']['tmp_name'] != "") {
						$change_name .= file_upload($_FILES['file']);
						@unlink(_DATA.$data->change_name);
						$add_sql .= ", file_name='{$_FILES['file']['name']}'";
						$add_sql .= ", file_size='{$_FILES['file']['size']}'";
						$add_sql .= ", change_name='{$change_name}'";
					}
					$add_sql .= " where idx='{$this->param->idx}'";
				break;
			}
			$column .= $add_sql;
			access($this->get_query($column),"완료되었습니다.","/");
		}

	}