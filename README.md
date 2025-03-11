# 概要

自宅サーバー向け、アニメや配信の録画保存に最適な総合メディアサーバー  

## 本プロジェクトと関連プロジェクト
- NeoArchive(本プロジェクト): 保存・再生
- ts-recoding-sets(近日公開予定): TS・BS/CSの録画・エンコード
- encoding-control-server: job-queueコントローラー(エンコードに使用)

## バージョンとリリース

本プロジェクトは現在開発中です。

# 開発環境

## Devcontainerを開始する

1. コマンドパレットを開く
1. Devcontainerを開始する
![Devcontainerを開始する](./docs/img/スクリーンショット%202025-02-23%20232148.png)
1. 現在のWorkspaceを選択
![現在のWorkspaceを選択](./docs/img/スクリーンショット%202025-02-23%20232156.png)
1. 実行する環境を選択
![実行する環境を選択](./docs/img/スクリーンショット%202025-02-23%20232134.png)
    - .devcontainer/api: APIサーバー及びOpenAPIの開発に最適
    - .devcontainer/web: フロントエンドの開発に最適

## APIを見る

1. ./oapi/api.ymlを開く
1. Swagger UIを開く
    1. コマンドパレットを開く
    1. Swagger UIを開く
    ![Swagger UIを開く](./docs/img/スクリーンショット%202025-02-23%20233003.png)

# 本プロジェクトに関して

## 注意事項

本プロジェクトを使用して、第三者が作成したコンテンツを扱う場合は、所管国の著作権法に従ってください。

## License

This project is licensed under the **GNU Affero General Public License v3.0** - see the [LICENSE](./LICENSE) file for details.
