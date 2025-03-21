package db

import (
	"log/slog"
)

func migrate() {
	slog.Info("Migrating database schema")

	if cfg.DbMode == "sqlite" {
		// SQLiteは初期で外部キー制約が無効なので有効化
		slog.Info("Enabling foreign key constraints")
		db.Exec("PRAGMA foreign_keys = ON;")
	}

	db.AutoMigrate(&Video{})
	db.AutoMigrate(&VideoLog{})
	db.AutoMigrate(&AnimeEpisode{})
	db.AutoMigrate(&Cast{})
	db.AutoMigrate(&AnimeSeason{})
	db.AutoMigrate(&Series{})
	db.AutoMigrate(&Tag{})
}
