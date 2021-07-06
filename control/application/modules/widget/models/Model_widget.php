<?php
class model_widget extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	function getInfoEmail($table)
	{
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->email;
	}
	
	
	function getInfoPassword($table)
	{
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->password;
	}
	
	
	function getInfoMailServer($table)
	{
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->mail_server;
	}
	
	function getInfoMailPort($table)
	{
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->mail_port;
	}
	
	
	function getInfoMailName($table)
	{
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->mail_name;
	}
	
	
	function getAdmins($table)
	{
		$this->db->where($table.".is_enabled",1);
		$this->db->where($table.".adminuser_levels_id !=",1);
		$query = $this->db->get($table);
		return $query;
	}
	
	
	function getList($table,$id)
	{
		$this->db->where($table.".id",$id);
		$query = $this->db->get($table);
		return $query;
	}
	
	function getDropdown($table,$sort,$wheres,$subquery=[]){
		if(empty($sort) || is_null($sort)){
			$sort = "ref_title";
		}
		if(count($wheres) > 0){
			$i = 0;
			foreach($wheres["field"] as $w_field){
				$w_val = $wheres["value"][$i];
				if(strpos($wheres['field'][0],'IN')){
					if($w_val > 0)
						$this->db->where($w_field.'('.$w_val.')');
				}else{
					$this->db->where($w_field,$w_val);
				}
				$i++;
			}
		}

		if($subquery){
			$i = 0;
			
			

			foreach($subquery["field"] as $s_field){
				$subs = "(SELECT ".$subquery['field'][$i]." FROM tbl_".$subquery['table'][$i]." WHERE tbl_".$subquery['table'][$i].".id = tbl_".$table.".".$subquery['ref_id'][$i].")";
				$alias = " AS ".$subquery['field'][$i];
				$this->db->select($table.".*, ".$subs.$alias);
				$i++;
			}

			$sort = $subs;

		}

		if($subquery){
			$this->db->order_by($sort,"asc");
		}else{
			$this->db->order_by($table.".".$sort,"asc");
		}

		$q = $this->db->get($table);
		return $q;
	}

	function setUpdate($table,$id,$publish,$user_id,$type)
	{
		
		$data = array(
			      $type=>$publish,
			      'modified_by'=>$user_id
			      );
		$this->db->where($table.'.id',$id);
		$this->db->update($table,$data);
	}
	
	function getMailTemplate($uri)
	{
		$this->db->where('url',$uri);
		$q = $this->db->get('email_templates');
		return $q;
	}
	
	
	function getMembershipRule($table,$ref_type,$rule)
	{
		$this->db->where("rule",$rule);
		$this->db->where("ref_type",$ref_type);
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->values;
	}
	
	
	function getExpiredProPerty($table,$ref_type)
	{
		$now = date("Y-m-d",now());
		$this->db->where("expired_date <=",$now);
		$this->db->where("ref_type",$ref_type);
		#$this->db->where("DATE(modify_date) < DATE_SUB(CURRENT_DATE(),INTERVAL ".$exp_agent." week)");
		$query = $this->db->get($table);
		return $query;
	}
	
	function deleteExpiredProPerty($table,$table_gallery,$id)
	{
		
		#select gallery first
		$this->db->where('property_id',$id);
		$query = $this->db->get($table_gallery);
		if($query->num_rows() > 0){
			foreach($query->result() as $r){
				if(!empty($r->file_image)){
					if(file_exists("uploads/".$r->file_image)){
						# echo  "delete file in uploads <br/>"; 
						unlink("uploads/".$r->file_image); 
					}
					if(file_exists("uploads/thumbs/".$r->file_image)){ 
						#echo  "delete file in thumbs <br/>"; 
						unlink("uploads/thumbs/".$r->file_image); 
					}
					if(file_exists("uploads/headlines/".$r->file_image)){
						#echo  "delete file in headlines <br/>";  
						unlink("uploads/headlines/".$r->file_image);
					}
				}
			}
			
			#delete
			#echo "property gallery with property ID ".$id." deleted";
			$this->db->where('property_id',$id);
			$this->db->delete($table_gallery);
		}
		
		#delete properties
		#echo "property with ID ".$id." deleted";
		$this->db->where('id',$id);
		$this->db->delete($table);
	}
	
	function getExpiredDp($table)
	{
		$now = date("Y-m-d",now());
		$this->db->where('payment_status','UNPAID');
		$this->db->where("expired_date <=",$now);
		$query = $this->db->get($table);
		return $query;
	}
	
	function deleteExpiredDp($table,$table_properties,$id)
	{
		#delete file DP invoice
		$this->db->where('id',$id);
		$this->db->where('payment_status','UNPAID');
		$q1 = $this->db->get($table);
		if($q1->num_rows() > 0){
			$r = $q1->row();
			$file = $r->invoice_number.".pdf";
			if(file_exists("invoices/unit-dp/".$file)){ unlink("invoices/unit-dp/".$file); }
			
			#update status property unit
			$data_update = array(
								"status"=>$r->properties_status
								);
			$this->db->where('id',$r->properties_id);
			$this->db->update($table_properties,$data_update);
			
			$this->db->where('id',$id);
			$this->db->delete($table);
		}
	}
	
}
?>