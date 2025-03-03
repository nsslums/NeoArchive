package config

type Define struct {
	DbMode       string `split_words:"true" default:"sqlite"`
	DbSqlitePath string `split_words:"true" default:"./data/db.sqlite"`
	DbMysqlDsn   string `split_words:"true"`
}
