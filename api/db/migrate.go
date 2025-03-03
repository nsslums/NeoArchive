package db

import (
	"log/slog"
)

func migrate() {
	slog.Info("Migrating database schema")

	db.AutoMigrate(&Series{})
	db.AutoMigrate(&Tag{})
	db.AutoMigrate(&AnimeSeason{})
	db.AutoMigrate(&AnimeEpisode{})
	db.AutoMigrate(&Video{})
	db.AutoMigrate(&VideoLog{})
}
