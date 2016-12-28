<?php

use Candy\Base\Controller;

Class RecipeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    // getでrecipe/にアクセスされた場合
    // 検索画面
    public function index()
    {
        var_dump(Input::get());
        $this->data['title'] = 'レシピ一覧';
        View::display('recipe/index.twig', $this->data);
    }

    // getでrecipe/createにアクセスされた場合
    // 入力画面と確認画面はここでする。
    public function create()
    {
        $this->data['title'] = 'レシピ新規作成';
        View::display('recipe/create.twig', $this->data);
    }

    // postでrecipe/にアクセスされた場合
    // 新規作成処理
    public function store()
    {

        $memberId = 1;
        $timestamp = time();

        // 後でConfとかでまとめる
        $videoFolderPath = $_SERVER['DOCUMENT_ROOT'] . "/candy/public/video/";
        $thumbFolderPath = $_SERVER['DOCUMENT_ROOT'] . "/candy/public/thumb/";
        $ffmpegAppPath = $_SERVER['DOCUMENT_ROOT'] . "/candy/app/cmd/ffmpeg";
        $userVideoFolderPath = $videoFolderPath . $memberId;

        //「$userVideoFolderPath」で指定されたディレクトリが存在するか確認
        if(!file_exists($userVideoFolderPath)){
            //存在しないときの処理（「$userVideoFolderPath」で指定されたディレクトリを作成する）
            if(mkdir($userVideoFolderPath, 0777)){
                //作成したディレクトリのパーミッションを確実に変更
                chmod($userVideoFolderPath, 0777);
            }else{
                //作成に失敗した時の処理
                echo "作成に失敗しました";
            }
        }

        $clip = Input::file('clip');
        // hogehoge.mpd -> hogehoge
        $clipFileName = pathinfo($clip['name'], PATHINFO_FILENAME);

        // ファイル名：filen
        $clipUploadFileName = "{$clipFileName}_{$timestamp}";

        // ファイルを移動 アップロード
        move_uploaded_file($clip['tmp_name'], "{$userVideoFolderPath}/{$clipUploadFileName}.mp4");

        $uploadFilePath = "{$userVideoFolderPath}/{$clipUploadFileName}.mp4";
        // http://qiita.com/tukiyo3/items/d8caac4fcf8ad5a7167b
        exec("{$ffmpegAppPath} -i {$uploadFilePath} -ss 5 -vframes 1 -f image2 -s 320x240 {$thumbFolderPath}{$uploadFilePath}.jpg");
        print "{$ffmpegAppPath} -i {$uploadFilePath} -ss 5 -vframes 1 -f image2 -s 320x240 {$thumbFolderPath}{$clipUploadFileName}.jpg";


        // NG
//        $Recipe = new Recipe();
////        $Recipe->load(Input::post());
//
//        $clip = Input::file('clip');
//        $name = md5(sha1(uniqid(mt_rand(), true))).'.'.$clip->getClientOriginalExtension();
//        $clip->move('media', $name);
//
//        try {
//            $Recipe->validate();
//            if ($Recipe->hasErrors()) {
////                $Recipe->save();
//                $this->app->redirect('recipe/create');
//                View::display('recipe/input.twig', $this->data);
//                $memberId = 1;
//                mkdir('video/' . 1, 0755);
//                exec('/app/cmd/ffmpeg -i video/' . $memberId . '/' . $clip . ' -ss 1 -vframes 1 -f image2 video/' . $memberId . '/thumb_' . $filename . '.jpg');
//            }
//        } catch (\Exception $e) {
//            print_r($e->getMessage());
//        }
    }

    // getでrecipe/:idにアクセスされた場合
    // 詳細画面
    public function show($id)
    {
        $this->data['title'] = '◯◯料理レシピ';
        View::display('recipe/show.twig', $this->data);
    }

    // getでrecipe/:id/editにアクセスされた場合
    // 編集画面
    public function edit($id)
    {
        $this->data['title'] = 'レシピ編集';
        View::display('recipe/input.twig', $this->data);
    }

    // putまたはpatchでrecipe/:idにアクセスされた場合
    // 更新処理
    public function update($id)
    {

    }

    // deleteでrecipe/:idにアクセスされた場合
    // 削除処理
    public function destroy($id)
    {

    }
}