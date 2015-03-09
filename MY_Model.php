protected function join_temporary_results($data)
    {
        $data = (sizeof($data)==1) ? array($data) : $data;
        $data = json_decode(json_encode($data), TRUE);
        foreach($this->_requested as $requested_key => $request)
        {
            $pivot_table = NULL;
            $relation = $this->_relationships[$request];
            $this->load->model($relation['foreign_model']);
            $foreign_key = $relation['foreign_key'];
            $local_key = $relation['local_key'];
            (isset($relation['pivot_table'])) ? $pivot_table = $relation['pivot_table'] : FALSE;
            $foreign_table = $relation['foreign_table'];
            $type = $relation['relation'];
            $relation_key = $relation['relation_key'];
            $local_key_values = array();
            foreach($data as $key => $element)
            {
                if(isset($element[$local_key]))
                {
                    $id = $element[$local_key];
                    $local_key_values[$key] = $id;
                }
            }
            if(!isset($pivot_table))
            {
                $sub_results = $this->{$relation['foreign_model']}->as_array()->where($foreign_key, $local_key_values)->get_all();
            }
            else
            {
                $this->_database->join($pivot_table, $foreign_table.'.'.$foreign_key.' = '.$pivot_table.'.'.singular($foreign_table).'_'.$foreign_key, 'right');
                $this->_database->join($this->table, $pivot_table.'.'.singular($this->table).'_'.$this->primary.' = '.$this->table.'.'.$this->primary,'right');
                $sub_results = $this->_database->get($foreign_table)->result_array();
            }

            if(isset($sub_results) && !empty($sub_results)) {
                $subs = array();
                foreach ($sub_results as $result) {
                    $subs[$result[$foreign_key]][] = $result;
                }
                $sub_results = $subs;
                foreach($local_key_values as $key => $value)
                {
                    if(array_key_exists($value,$sub_results))
                    {
                        if ($type == 'has_one')
                        {
                                $data[$key][$relation_key] = $sub_results[$value][0];
                        }
                        else
                        {
                                $data[$key][$relation_key] = $sub_results[$value];
                        }
                    }
                }
            }
            unset($this->_requested[$requested_key]);
        }
        if(sizeof($data)==1) $data = $data[0];
        return ($this->return_as == 'object') ? json_decode(json_encode($data), FALSE) : $data;
    }
