<?php
/**
 * Migration Task class.
 */
class MigrationReport
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
      // add the post-migration code here
  }

  public function preDown()
  {
      // add the pre-migration code here
  }

  public function postDown()
  {
      // add the post-migration code here
  }

  public function getUpSQL()
  {
    return <<<END
-- WP32 課題9 サポートファイル
-- 
-- テーブル作成、データ登録SQLファイル
-- sharerepousrユーザで使用
-- コマンド: mysql -u sharerepousr -p sharerepo --default-character-set=utf8 < "/…/2015WP32Asgmt09_dbinit_UTF8.sql"
-- 
-- @author Shinzo SAITO

-- テーブル削除
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS reportcates;

-- ユーザ
CREATE TABLE users
(
	id int NOT NULL AUTO_INCREMENT COMMENT 'ユーザID',
	us_mail text NOT NULL COMMENT 'メールアドレス',
	us_name text NOT NULL COMMENT '名前',
	us_password text NOT NULL COMMENT 'パスワード',
	-- 0=終了
	-- 1=管理者
	-- 2=一般
	us_auth int DEFAULT 2 NOT NULL COMMENT '権限 : 0=終了
1=管理者
2=一般',
	PRIMARY KEY (id)
) COMMENT = 'ユーザ';

-- レポート
CREATE TABLE reports
(
	id int NOT NULL AUTO_INCREMENT COMMENT 'レポートID',
	rp_date date NOT NULL COMMENT '作業日',
	rp_time_from time NOT NULL COMMENT '作業開始時間',
	rp_time_to time NOT NULL COMMENT '作業終了時間',
	rp_content text NOT NULL COMMENT '作業内容',
	rp_created_at datetime NOT NULL COMMENT '登録日時',
	reportcate_id int NOT NULL COMMENT '作業種類ID',
	user_id int NOT NULL COMMENT '報告者ID',
	PRIMARY KEY (id)
) COMMENT = 'レポート';

-- 作業種類
CREATE TABLE reportcates
(
	id int NOT NULL AUTO_INCREMENT COMMENT '作業種類ID',
	rc_name text NOT NULL COMMENT '種類名',
	rc_note text COMMENT '備考',
	-- 0=非表示
	-- 1=表示
	rc_list_flg int DEFAULT 1 NOT NULL COMMENT 'リスト表示の有無 : 0=非表示
1=表示',
	rc_order int DEFAULT 0 NOT NULL COMMENT '表示順序',
	PRIMARY KEY (id)
) COMMENT = '作業種類';

-- 外部キー作成(reports-reportcates)
ALTER TABLE reports
	ADD FOREIGN KEY (reportcate_id)
	REFERENCES reportcates (id)
	ON UPDATE CASCADE
	ON DELETE CASCADE
;

-- 外部キー作成(reports-users)
ALTER TABLE reports
	ADD FOREIGN KEY (user_id)
	REFERENCES users (id)
	ON UPDATE CASCADE
	ON DELETE CASCADE
;


-- ユーザデータ挿入
INSERT INTO users (us_mail, us_name, us_password, us_auth) VALUES ('architshin@websarva.com', '齊藤新三', 'hogehoge', '1');

-- 作業種類データ挿入
INSERT INTO reportcates (rc_name, rc_note, rc_list_flg, rc_order) VALUES ('実装', '', '1', '1');
INSERT INTO reportcates (rc_name, rc_note, rc_list_flg, rc_order) VALUES ('打合せ', '', '1', '2');
INSERT INTO reportcates (rc_name, rc_note, rc_list_flg, rc_order) VALUES ('資料作成', '', '1', '3');
INSERT INTO reportcates (rc_name, rc_note, rc_list_flg, rc_order) VALUES ('顧客対応', '', '1', '4');
INSERT INTO reportcates (rc_name, rc_note, rc_list_flg, rc_order) VALUES ('設計', '', '1', '5');
INSERT INTO reportcates (rc_name, rc_note, rc_list_flg, rc_order) VALUES ('その他', '', '1', '6');
END;
  }


  public function getDownSQL()
  {
    return <<<END
DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS reportcates;
END;
  }

}