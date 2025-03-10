package config

type Define struct {
	DbMode       string `split_words:"true" default:"sqlite"`
	DbSqlitePath string `split_words:"true" default:"./data/db.sqlite"`
	DbMysqlDsn   string `split_words:"true"`
	ApiPort      int16  `split_words:"true" default:"1323"`
}
