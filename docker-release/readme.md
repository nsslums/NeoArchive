# デプロイ方法

## デフォルト構成

### コンテナ
- appコンテナ
- webコンテナ
- (オプション)dbコンテナ

### 必須要件
- 外部データベースサーバー
- 外部リバースプロキシーサーバー

## appコンテナの永続ボリューム

以下のディレクトリを永続化  
/mnt/persistent-volume  

以下のディレクトリを作成  
```
mkdir -p /mnt/persistent-volume/iass/data
mkdir -p /mnt/persistent-volume/iass/dustbox
mkdir -p /mnt/persistent-volume/iass/uploadtmp
mkdir -p /mnt/persistent-volume/iass/userdata
```

以下のディレクトリ配下をすべてwww-dataオーナーにする  
```
chown -R www-data:www-data /mnt/persistent-volume/iass
```

## webコンテナへのアクセス

auth: v4とv6のhttp 80番  
iass: v4とv6のhttp 81番  

※Kubernetes IngressなどのReverse Proxy Server経由でのアクセスを想定しています。  

## 環境変数

### appコンテナ

データベースの接続先  
```
DB_HOST
```

cookieとphpが信頼するドメイン  
```
HTTP_S_DOMAIN
```

プロトコル(http又はhttps)  
```
HTTP_SCHEMA
```

例  
```
 - DB_HOST=127.0.0.1
 - HTTP_S_DOMAIN=example.com
 - HTTP_SCHEMA=https
```
