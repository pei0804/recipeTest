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
        $Recipe = new Recipe();
        $Recipe->load(Input::post());
        $Recipe->validate();

        if ($Recipe->hasErrors()) {
            $this->data['title'] = 'レシピ新規作成';
            print_r($Recipe->getErrors()); // return errors
            $this->app->redirect('recipe/create');
            View::display('recipe/input.twig', $this->data);
        }
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