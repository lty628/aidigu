<?php

namespace app\cms\controller;

use app\cms\model\Attachment as AttachmentModel;
use think\Controller;


class Attachment extends Controller
{
    // 发表文章
    public function add(AttachmentModel $attachment)
    {
        $userId = getUserId();
        if (!$userId) return ajaxJson(0, '未登录');
        $info = input('post.info');
        $info['uid'] = $userId;
        // 处理数据
        $info = array_merge($this->handleAttach($info), $info);
        $result = $attachment->add($info);
        if (!$result) return ajaxJson(0, '添加失败');
        // $attachmentId = $attachment->id;
        return ajaxJson(1, '添加成功', ['url' => '/blog/filelist.html']);
    }

    public function readCount(AttachmentModel $attachment)
    {
        $userId = getUserId();
        $attachId = input('post.attach_id');
        if (!$attachId) return ajaxJson(0, '参数不正确');
        if (!$userId) return ajaxJson(0, '未登录');
        $attachment->where(['attach_id' => $attachId])->setInc('read_count');
        return ajaxJson(1, '');
    }

    public function download()
    {
        $attachId = input('get.attach_id');
        $userId = getUserId();
        if (!$userId) return $this->error('未登录');
        $attachment = new AttachmentModel();
        $info = $attachment->getOne(['attach_id' => $attachId]);
        if (!$info) return $this->error('无此附件！');
        $attachment->where(['attach_id' => $attachId])->setInc('download_count');
        return $this->header($info);
    }

    public function header($info)
    {
        $filePath = $info['attach_path'];
        $name = ($info['attach_title'] ?: uniqid()) . '.pdf';
        //下载文件
        if (!file_exists($filePath)) {
            $this->error("找不到文件");
            exit;
        } else {
            header("Content-Type:text/html;charset=utf-8");
            header("Content-type:application/force-download");
            header("Content-Type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-Length:" . filesize($filePath)); //指定下载文件的大小
            header('Content-Disposition:attachment;filename="' . $name . '"');
            //必要时清除缓存
            ob_clean();
            flush();
            readfile($filePath);
            exit();
        }
    }

    protected function handleAttach($info)
    {
        if ($info['attach_content']) {
            return $this->writeAttachPath($info);
        }
        return $this->readAttachPath($info);
    }

    protected function writeAttachPath($info)
    {
        return $info;
        // $htmlStr = '<html>
        // <head>
        // <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><style>'. 1 .'</style>';
        // $htmlStr .= '<body><div>' . $info['attach_content_preview'] . '</div></body>
        // </html>';
        // dump($htmlStr);

        try {
            $html2pdf = new Html2Pdf('P', 'A4', 'cn', true, 'utf-8');
            $html2pdf->pdf->SetFont("stsongstdlight", "", 12);
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML($info['attach_content_preview']);
            $html2pdf->output(env('ROOT_PATH') . 'public/test.pdf', 'F');
        } catch (Html2PdfException $e) {
            $html2pdf->clean();
            $formatter = new ExceptionFormatter($e);
            echo $formatter->getHtmlMessage();
        }


        return $info;











        // echo $htmlStr;die;
        $options = new \Dompdf\Options();
        $options->set('enable_remote', TRUE);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($htmlStr, 'UTF-8');
        // $dompdf->setPaper('A4', 'landscape');
        // Render the HTML as PDF
        $dompdf->render();
        $pdf = $dompdf->output(array("Attachment" => 0));
        file_put_contents('test.pdf', $pdf);
        // 调用包写入 pdf
        return $info;
    }

    protected function readAttachPath($info)
    {
        // 读取文件 获取 html
        $filePath = $info['attach_path'];
        // 读取
        return $info;
    }

    public function test()
    {
        $content = '<div style="display: block; width: 100%"><div previewcontainer="true" style="padding: 20px;"><p>Windows在电脑 -&gt; 系统 -&gt; 高级系统设置 -&gt; 用户环境中分别新建GO111MODULE和GOPROXY两个用户变量，其值如下图所示：</p>
        <p><code>GO111MODULE=on GOPROXY=https://mirrors.aliyun.com/goproxy/</code></p>
        <p><a href="https://img-blog.csdnimg.cn/20200218205411529.png"><img src="https://img-blog.csdnimg.cn/20200218205411529.png" alt="在这里插入图片描述"></a><br>配置好之后，Windows + R调出终端，输入cmd，通过go env命令查看go的环境变量配置是否设置成功。</p>
        <h2 id="h2-vscode-"><a name="vscode中配置"></a><span></span>vscode中配置</h2><p>vscode编辑器的设置在：文件 -&gt; 首选项 -&gt; 设置 -&gt; 用户 -&gt; 应用程序 -&gt; 代理服务器路径下，如下图所示：<br><a href="https://img-blog.csdnimg.cn/20200218205056649.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2xvdmU2NjY2NjZzaGVu,size_16,color_FFFFFF,t_70"><img src="https://img-blog.csdnimg.cn/20200218205056649.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2xvdmU2NjY2NjZzaGVu,size_16,color_FFFFFF,t_70" alt="在这里插入图片描述"></a></p>
        </div></div>';

        // $html2pdf = new Html2Pdf('P', 'A4', 'fr',true, 'utf-8');
        // $html2pdf->pdf->autoScriptToLang = true;
        // $html2pdf->pdf->autoLangToFont = true;
        // // $html2pdf->pdf->AddPage();
        // $html2pdf->pdf->SetFont("stsongstdlight", "", 12, '', true);

        // $html2pdf->pdf->SetDisplayMode('fullpage');

        // $html2pdf->writeHTML($content);
        // $html2pdf->output('asdf.pdf');
        // exit;

        $pdf = new \Mpdf\Mpdf();
        $title = 'asd噶士大夫';
        $pdf->SetAuthor('作者');
        $pdf->SetTitle($title);
        $pdf->SetSubject('项目');
        $pdf->SetKeywords('关键词,关键词');

        // // 设置左、上、右的间距
        $pdf->SetMargins('10', '10', '10');
        // // 设置是否自动分页  距离底部多少距离时分页
        $pdf->SetAutoPageBreak(TRUE, '15');

        //开启字段文字和样式
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->AddPage();
        // 设置字体
        $pdf->SetFont('stsongstdlight', '', 14);
        $pdf->SetDisplayMode('fullwidth');

        //设置背景
        // $pdf->SetWatermarkImage('背景图路径', 0.1, [230, 180]);
        // $pdf->showWatermarkImage = true;

        //标题
        $title = '<h1 style="text-align: left;">' . $title . '</h1><p style="color: grey;font-size: 10px;">版本：1.0</p>
        <span style="color: grey;font-size: 10px;">发布/生效日期：2020 年 8 月 10 日</span><hr style="color: grey;">';

        $pdf->WriteHTML($title . $content);
        $pdf->Output();
        exit();
    }
}
