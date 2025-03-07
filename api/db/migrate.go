package db

import (
	"log/slog"
)

func migrate() {
	slog.Info("Migrating database schema")

	db.AutoMigrate(&Video{})
	db.AutoMigrate(&VideoLog{})
	db.AutoMigrate(&AnimeEpisode{})
	db.AutoMigrate(&AnimeSeason{})
	db.AutoMigrate(&Series{})
	db.AutoMigrate(&Tag{})
}
