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
        //---------SESSIONで取る------------
        $memberId = 1;

        // ????: 判定するものが多すぎるのて一旦flgで判定する
        $isError = false;

        //---------------------
        $recipeInput = Input::post();
        App::flash('input', Input::post());
        $clip = Input::file('clip');
        if (!is_uploaded_file($clip["tmp_name"])) {
            App::flash('video', 'ビデオがアップロードできませんでした');
            $isError = true;
        } else if ($clip["type"] != "video/mp4") {
            App::flash('video', 'ビデオはMP4形式のみ対応しています');
            $isError = true;
        }

        // hogehoge.mpd -> hogehoge
        $clipFileName = pathinfo($clip['name'], PATHINFO_FILENAME);

        // ファイル名：filename_timestamp
        $timestamp = time();
        $clipUploadFileName = "{$clipFileName}_{$timestamp}";

        //----------recipeテーブル処理--------------//
        $Recipe = new Recipe();
        $recipeInput['member_id'] = 1;
        $recipeInput['thumb'] = "{$clipUploadFileName}.jpg";
        $recipeInput['clip'] = "{$clipUploadFileName}.mp4";
        $Recipe->load($recipeInput);
        $Recipe->validate();

        // エラーがあればこの時点で格納しておく
        if (!$Recipe->hasErrors()) {
            App::flash('recipe', $Recipe->getErrors());
            $isError = true;
        }

        //----------ingredientsテーブル処理--------------//
        $Ingredients = new Ingredients();
        $ingredientsInput = Input::post();
        $checkValue = [];
        $ingredientsInserts = [];
        $ingredientsInputError = [];
        $ingredientsNo = 1;

        // HACK: eloquant 5.3系ならまとめてvaldiationできる
        for ($i = 0; $i < count($ingredientsInput['name']); $i++) {
            $checkValue['name'] = $ingredientsInput['name'][$i];
            $checkValue['quantity'] = $ingredientsInput['quantity'][$i];
            $Ingredients->load($checkValue);
            $Ingredients->validate();
            if(!$Ingredients->hasErrors()) {
                $checkValue['quantity'] = $ingredientsNo;
                $ingredientsNo++;
            }
            $ingredientsInputError[] = $Ingredients->getErrors();
            $ingredientsInserts[] = $checkValue;
        }
        App::flash('ingredients', $ingredientsInserts);

        if (count($ingredientsInputError) != 0) {
            App::flash('errorIngredients', $Recipe->getErrors());
            $isError = true;
        }

        //----------フォルダ作成（なければ）-------------/

        // TODO: 本来はCONFクラスにまとめる
        $ffmpegAppPath = $_SERVER['DOCUMENT_ROOT'] . "/candy/app/cmd/ffmpeg";
        $thumbFolderPath = $_SERVER['DOCUMENT_ROOT'] . "/candy/public/thumb/";
        $videoFolderPath = $_SERVER['DOCUMENT_ROOT'] . "/candy/public/video/";
        $userVideoFolderPath = $videoFolderPath . $memberId;
        $userThumbFolderPath = $thumbFolderPath . $memberId;

        // フォルダ作成
        //「$userVideoFolderPath」で指定されたディレクトリが存在するか確認
        if (!file_exists($userVideoFolderPath)) {
            //存在しないときの処理（「$userVideoFolderPath」で指定されたディレクトリを作成する）
            if (mkdir($userVideoFolderPath, 0777)) {
                //作成したディレクトリのパーミッションを確実に変更
                chmod($userVideoFolderPath, 0777);
            } else {
                App::flash('video', 'アップロード準備に失敗しました。もう一度お試しください');
                $isError = true;
            }
        }

        if (!file_exists($userThumbFolderPath)) {
            if (mkdir($userThumbFolderPath, 0777)) {
                chmod($userThumbFolderPath, 0777);
            } else {
                App::flash('video', 'アップロード準備に失敗しました。もう一度お試しください');
                $isError = true;
            }
        }

        //----------動画アップロード--------------//
        move_uploaded_file($clip['tmp_name'], "{$userVideoFolderPath}/{$clipUploadFileName}.mp4");
        $uploadFilePath = "{$userVideoFolderPath}/{$clipUploadFileName}.mp4";
        if (!file_exists($uploadFilePath)) {
            App::flash('video', 'ビデオアップロードに失敗しました。もう一度お試しください');
            $isError = true;
        }

        //----------サムネイル作成--------------//
        // http://qiita.com/tukiyo3/items/d8caac4fcf8ad5a7167b
        exec("{$ffmpegAppPath} -i {$uploadFilePath} -ss 5 -vframes 1 -f image2 -s 320x240 {$userThumbFolderPath}/{$clipUploadFileName}.jpg");
        if (!file_exists("{$thumbFolderPath}{$uploadFilePath}.jpg")) {
            App::flash('video', 'サムネイル作成に失敗しました。もう一度お試しください');
            $isError = true;
        } else {
            // TODO: エラー処理
            unlink($uploadFilePath);
        }
        //-------------判定---------------
        if ($isError) {
            App::flash('messageError', "登録に失敗しました。入力内容をご確認ください");
        }

        $db = \DB::getConnection();
        try {
            $db->beginTransaction();
            $Recipe->save();
            $recipeId = $Recipe->getConnection()->getPdo()->lastInsertId();
            // RecipeID取得のためループ
            for ($i = 0; $i < count($ingredientsInserts); $i++) {
                $ingredientsInserts[$i]['id'] = $recipeId;
                $Ingredients->load($ingredientsInserts);
                $Ingredients->validate();
                if(!$Ingredients->hasErrors()) {
                    $Ingredients->save();
                }
            }
            $db->commit();
            App::flash('messageSuccess', "登録が完了しました");
            Response::redirect($this->siteUrl('recipe') . '/' . $recipeId);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            $db->rollBack();
        }

//        $db = \DB::getConnection();
//        try {
//            $db->beginTransaction();
//            $Recipe->save();
//            $recipeId = $Recipe->getConnection()->getPdo()->lastInsertId();
//
//            $ingredientsNo = 1;
//            for ($i = 0; $i < count($ingredientsInserts); $i++) {
//                if(strlen($ingredientsInserts[$i]['name']) != 0 && strlen($ingredientsInserts[$i]['quantity']) != 0) {
//                    $ingredientsInserts[$i]['id'] = $recipeId;
//                    $ingredientsInserts[$i]['ingredients_no'] = $ingredientsNo;
//                    $ingredientsNo++;
//                    DB::table('ingredients')->insert($ingredientsInserts[$i]);
//
//                }
//            }
//            DB::table('ingredients')->insert($ingredientsInserts);
//            $db->commit();
//            App::flash('messageSuccess', "登録が完了しました");
////            Response::redirect($this->siteUrl('recipe') . '/' . $recipeId);
//        } catch (\Exception $e) {
//            print_r($e->getMessage());
//            $db->rollBack();
////            Response::redirect($this->siteUrl('recipe/create'));
//        }

    }

    // getでrecipe/:idにアクセスされた場合
    // 詳細画面
    public function show($id)
    {
        $Recipe = new Recipe();
        try {
            $findRecipe = $Recipe::findOrFail($id);
            $this->data['title'] = $findRecipe->title;
            $this->data['recipe'] = $findRecipe;
            App::render('recipe/show.twig', $this->data);
        } catch (\SQLiteException $e) {
            App::flash('messageError', "データベースエラーが発生しました。管理者にお問い合わせください。");
            Response::redirect($this->siteUrl('recipe'));
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            App::flash('messageError', "存在しないレポートが指定されました。");
            Response::redirect($this->siteUrl('recipe'));
        }
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