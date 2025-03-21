# 開発環境

## 前提

[環境](../README.md)

## 実行

```
api$ go run main.go
```

## OASからAPI Interfaceを作成する

```
api$ oapi-codegen -package apiInterface ../oapi/api.yml > ./apiInterface/apiInterface.go
```
