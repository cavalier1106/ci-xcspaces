<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Csv extends CI_Controller {

	function __construct()
	{
		parent::__construct();
        $this->load->database();
        header("Content-type: text/html; charset=utf-8");
	}

	/*
	*@ 导出CSV文件
	*/
	public function setCsv()
	{
		include('./jec_framework/Jec/libraries/Net.php');
		include('./jec_framework/Jec/libraries/CSV.php');

		$filename = 'xcspaces.com.csv';

        $node_tree = $this->db->dbprefix('node_tree');
        $sql = "select * from `{$node_tree}`";
        $data = $this->db->query($sql)->result_array();
		
		$csv = new MyCsv();
        $csv->setEncoding('GBK');
        $row_menu = array(_('空间ID'),_('空间名'),_('空间摘要'),_('空间标题'), _('空间关键字'),_('空间描述'));

        $csv->addRow($row_menu);
        foreach($data as $val)
        {
            $row_data = array(
                            $val['zid'],
                            $val['name'],
                            $val['summary'],
                            $val['title'],
                            $val['keyworks'],
                            $val['description'],
                         );
            
            $csv->addRow($row_data);
        }
        $csv->download($filename);
        exit;
	}

    /*
    *@ 获取CSV文件
    */
    public function getCsv()
    {
        $filename = 'xcspaces.com.csv';
        $file = fopen('./doc/' . $filename, "r");
        while ($fd = fgetcsv($file)) 
        {
            //每次读取CSV里面的一行内容
            $data[] = $fd;
        }

        // echo "<pre>";var_dump( $data );exit;
        $outData = array();
        foreach ($data as $k => $v) {
            $outData[$k]['zid'] = $v[0];
            $outData[$k]['name'] = $v[1];
            $outData[$k]['summary'] = $v[2];
            $outData[$k]['title'] = $v[3];
            $outData[$k]['keyworks'] = $v[4];
            $outData[$k]['description'] = $v[5];   
        }
        // echo "<pre>";var_dump( $outData );exit;
        foreach ($outData as $k => $v) 
        {
            // var_dump(iconv('gb2312', 'utf-8', $v['name']));exit;
            $updata = array(
                    'name' => iconv('gb2312', 'utf-8', $v['name']),
                    'summary' => iconv('gb2312', 'utf-8', $v['summary']),
                    'title' => iconv('gb2312', 'utf-8', $v['title']),
                    'keyworks' => iconv('gb2312', 'utf-8', $v['keyworks']),
                    'description' => iconv('gb2312', 'utf-8', $v['description']),
                );
            $this->db->where('zid', $v['zid']);
            $this->db->update('node_tree', $updata);
        }

        exit(0);
    }

}
