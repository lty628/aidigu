<?php
/**
 * 仅做演示,请勿生产环境使用
 */
define('DS', DIRECTORY_SEPARATOR);
define('DIR_IMAGE', 'upload/');

$param = $_REQUEST;
$fm = new ImageManager();
$fm->init_dir('');

if ($param['action']) {
    $rs = call_user_func_array([ & $fm, $param['action']], [$param]);
    echo json_encode($rs);
}

class ImageManager
{
    //用户图片目录
    protected $user_image_dir;
    //图片保存路径
    protected $upload_url;
    protected $module;
    public function init_dir($user_image_dir, $module = 'admin')
    {
        $this->user_image_dir = $user_image_dir;
        $this->module = $module;
        $this->upload_url = DIR_IMAGE . $this->user_image_dir;
        if (!is_dir($this->upload_url)) {
            mkdir($this->upload_url, 0777);
            chmod($this->upload_url, 0777);
            fopen($this->upload_url . '/index.html', 'wb');
        }
    }

    public function get_dir($d)
    {
        $dir = '';
        foreach ($d as $k => $v) {
            $dir .= $v . '/';
        }
        $dir = substr($dir, 0, strlen($dir) - 1);
        return $dir;
    }
    //图片上传
    public function upload()
    {
        $json = ['code' => 0];
        $data = $_REQUEST;

        // Make sure we have the correct directory
        if (isset($data['path'])) {
            $d = explode('-', $data['path']);
            $dir = '';
            if (count($d) > 1) {
                $dir = $this->get_dir($d);
            } else {
                $dir = $data['path'];
            }
            $directory = rtrim($this->upload_url . '/' . str_replace(array('../', '..\\', '..'), '', $dir), '/');
        } else {
            $directory = $this->upload_url;
        }
        // Check its a directory
        if (!is_dir($directory)) {
            $json['msg'] = '不是一个文件夹';
        }
        if (empty($json['msg'])) {
            $i = 0;
            foreach ($_FILES as $file) {
                foreach ($file['name'] as $key => $val) {
                    $files[$i]['name'] = $file['name'][$key];
                    $files[$i]['type'] = $file['type'][$key];
                    $files[$i]['tmp_name'] = $file['tmp_name'][$key];
                    $files[$i]['error'] = $file['error'][$key];
                    $files[$i]['size'] = $file['size'][$key];
                    $i++;
                }
            }
            foreach ($files as $fileInfo) {
                if ($fileInfo['error'] === UPLOAD_ERR_OK) {
                    if (!@move_uploaded_file($fileInfo['tmp_name'], $directory . '/' . $fileInfo['name'])) {
                        $json['msg'][] = $fileInfo['name'] . '文件移动失败';
                    }
                    $json['inof'][] = $directory . '/' . $fileInfo['name'];
                } else {
                    $json['msg'][] = $fileInfo['error'];
                }
            }
        }

        if (empty($json['msg'])) {
            $json['code'] = 1;
            $json['msg'] = '上传成功';
        }
        return $json;
    }
    //新建文件夹
    public function folder()
    {
        $json = ['code' => 0];
        $data = $_REQUEST;
        // Make sure we have the correct directory
        if (isset($data['path'])) {
            $d = explode('-', $data['path']);
            $dir = '';
            if (count($d) > 1) {
                $dir = $this->get_dir($d);
            } else {
                $dir = $data['path'];
            }
            $directory = rtrim($this->upload_url . '/' . str_replace(array('../', '..\\', '..'), '', $dir), '/');
        } else {
            $directory = $this->upload_url;
        }
        // Check its a directory
        if (!is_dir($directory)) {
            $json['msg'] = '文件夹不存在';
        }
        if (empty($json['msg'])) {
            $post = $_REQUEST;
            // Sanitize the folder name
            $folder = str_replace(array('../', '..\\', '..'), '', basename(html_entity_decode($post['folder'], ENT_QUOTES, 'UTF-8')));

            // Validate the filename length
            if ((mb_strlen($folder) < 1) || (mb_strlen($folder) > 128)) {
                $json['msg'] = '文件夹长度必须大于0小于128';
            }
            if ($folder == $this->user_image_dir) {
                $json['msg'] = '非法名称';
            }
            if (!preg_match('/^[A-Za-z0-9]+$/', $folder)) {
                $json['msg'] = '文件夹名称,只允许包含字母、数字';
            }
            // Check if directory already exists or not
            if (is_dir($directory . '/' . $folder)) {
                $json['msg'] = '文件夹已经存在';
            }
        }
        if (empty($json['msg'])) {
            mkdir($directory . '/' . $folder, 0777);
            chmod($directory . '/' . $folder, 0777);
            fopen($directory . '/' . $folder . '/index.html', 'wb');
            $json['code'] = 1;
            $json['msg'] = '创建成功';
        }
        return $json;
    }
    //删除图片和文件夹
    public function delete()
    {
        $json = [];
        $post = $this->post();
        if (isset($post['path'])) {
            $paths = $post['path'];
        } else {
            $json['error'] = '非法操作';
            return $json;
        }
        // Loop through each path to run validations
        foreach ($paths as $path) {
            $path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');
            // Check path exsists
            if ($path == DIR_IMAGE . 'images' || $path == DIR_IMAGE . 'images/' . $this->user_image_dir) {
                $json['error'] = '非法操作';
                break;
            }
        }
        if (!$json) {
            // Loop through each path
            foreach ($paths as $path) {
                $path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');
                // If path is just a file delete it
                if (is_file($path)) {
                    unlink($path);
                    // If path is a directory beging deleting each file and sub folder
                } elseif (is_dir($path)) {
                    $files = array();
                    // Make path into an array
                    $path = array($path . '*');
                    // While the path array is still populated keep looping through
                    while (count($path) != 0) {
                        $next = array_shift($path);
                        foreach (glob($next) as $file) {
                            // If directory add to path array
                            if (is_dir($file)) {
                                $path[] = $file . '/*';
                            }
                            // Add the file to the files to be deleted array
                            $files[] = $file;
                        }
                    }
                    // Reverse sort the file array
                    rsort($files);
                    foreach ($files as $file) {
                        // If file just delete
                        if (is_file($file)) {
                            @unlink($file);
                            // If directory use the remove directory function
                        } elseif (is_dir($file)) {
                            @rmdir($file);
                        }
                    }
                }
            }
            $file = new \EasySwoole\Utility\File();
            $file->deleteDirectory(DIR_IMAGE . '/cache/images/');
            $json['success'] = '删除成功';
        }
        return $this->send($json);
    }
    //生成测试数据
    public function get_file_data_vm($data){
        $limit = isset($data['limit']) ? $data['limit'] : 16;
        $_ext_arr = ['ai','dir','fonts','mm','pages','txt','zip','apk','doc','ipa','mmap','pdf','visio','bt','eps','keynote','mp3','ppt','web','cad','exe','links','mp4','ps','xls','code','fla','misc','number','rar','xmind'];
        $_realpic_arr = [
            'https://xzcustomer.cdn.bcebos.com./uploadfile/category/4e/3e/a1/06/5c49b27d7c104.jpg'
            ,'https://xzcustomer.cdn.bcebos.com./uploadfile/category/d1/62/6b/cc/5c49b2f1a7f01.jpg'
            ,'https://xzcustomer.cdn.bcebos.com./uploadfile/category/61/f6/fa/62/5c49b2babb9f5.jpg'
            ,'https://xzcustomer.cdn.bcebos.com./uploadfile/category/f9/2a/45/b4/5c49b263c7aa9.jpg'
            ,'https://xzcustomer.cdn.bcebos.com./uploadfile/category/d9/48/18/58/5c49b26f4be9f.jpg'
            ,'https://xzcustomer.cdn.bcebos.com./uploadfile/category/05/d0/64/21/5c49b2a0c215a.jpg'
            ,'https://xzcustomer.cdn.bcebos.com./uploadfile/category/3e/3e/9a/ae/5c49b2e4473da.jpg'
            ,''
        ];
        for ($i=0; $i < $limit-5; $i++) { 
            $_ext = $_ext_arr[array_rand($_ext_arr)];
            $_name = $this->createRandomStr(5);
            $name = $_ext == 'dir' ? $_name : $_name.'.'.$_ext;
            $type = $_ext == 'dir' ? 'directory' : $_ext;
            $path = $_ext == 'dir' ? $_name : $name;
            $rs['images'][] = [
                'thumb'=>$_name.'.'.$_ext
                ,'name'=>$name
                ,'type'=>$type
                ,'path'=>$path
            ];
        }
        for ($i=0; $i < 5; $i++) {
            $name = $_realpic_arr[array_rand($_realpic_arr)];
            $rs['images'][] = [
                'thumb'=>$_realpic_arr[array_rand($_realpic_arr)]
                ,'name'=>$this->createRandomStr(5).'jpg'
                ,'type'=>'jpg'
                ,'path'=>$name
            ];
        }
        shuffle($rs['images']);
        $rs['count'] = $limit*mt_rand(1,5);
        return $rs;
    }
    private function  createRandomStr($length){
        $str = array_merge(range(0,9),range('a','z'),range('A','Z'));
        shuffle($str);
        $str = implode('',array_slice($str,0,$length));
        return $str;
    }
    //取得图片文件，文件夹数据，分页数据
    public function get_file_data($data)
    {
        if (isset($data['filter_name'])) {
            $filter_name = rtrim(str_replace(array('../', '..\\', '..', '*'), '', $data['filter_name']), '/');
        } else {
            $filter_name = null;
        }
        //验证是否有这个文件夹
        if (isset($data['path'])) {

            $dir = $data['path'];

            $directory = rtrim($this->upload_url . '/' . str_replace(array('../', '..\\', '..'), '', $dir), '/');
        } else {
            $directory = $this->upload_url;
        }

        if (isset($data['page'])) {
            $page = $data['page'];
        } else {
            $page = 1;
        }
        $limit = isset($data['limit']) ? $data['limit'] : 16;
        $images_and_directory = [];
        //取得文件夹
        $directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);
        if (!$directories) {
            $directories = array();
        }
        //取得文件
        $files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);
        if (!$files) {
            $files = array();
        }
        //合并文件夹和文件
        $images = array_merge($directories, $files);
        //计算文件和文件夹数量
        $image_total = count($images);
        // Split the array based on current page number and max number of items per page of 10
        $images = array_splice($images, ($page - 1) * $limit, $limit);
        if (empty($images)) {
            return ['images' => []];
        }
        foreach ($images as $image) {
            $name = str_split(basename($image), 14);
            if (is_dir($image)) {
                $url = [];
                $path = mb_substr($image, mb_strlen(DIR_IMAGE), mb_strlen($image));
                $p = explode('/', $path);
                if (count($p) > 1) {
                    $d = '';
                    foreach ($p as $k => $v) {
                        if ($v != 'images' && $v != $this->user_image_dir) {
                            $d .= $v . '-';
                        }
                    }
                    $d = substr($d, 0, strlen($d) - 1);
                    $url['directory'] = $d;
                } else {
                    $url['directory'] = end($p);
                }
                if (isset($data['target'])) {
                    $url['target'] = $data['target'];
                }
                if (isset($data['thumb'])) {
                    $url['thumb'] = $data['thumb'];
                }
                $images_and_directory['images'][] = array(
                    'thumb' => '',
                    'name' => implode(' ', $name),
                    'type' => 'directory',
                    'path' => $path,
                );
            } elseif (is_file($image)) {
                $images_and_directory['images'][] = array(
                    'thumb' => $image,
                    'name' => implode(' ', $name),
                    'type' => 'image',
                    'path' => mb_substr($image, mb_strlen(DIR_IMAGE), mb_strlen($image)),
                );
            }
        }

        $images_and_directory['count'] = $image_total;
        return $images_and_directory;
    }

}
