package db

import (
	"time"

	"gorm.io/gorm"
)

type Series struct {
	gorm.Model
	ID          uint `gorm:"primaryKey,autoIncrement"`
	Title       string
	AnimeSeason []AnimeSeason `gorm:"constraint:OnUpdate:RESTRICT,OnDelete:CASCADE;"`
}

type Tag struct {
	gorm.Model
	ID   uint `gorm:"primaryKey,autoIncrement"`
	Name string
}

type AnimeSeason struct {
	gorm.Model
	ID           uint `gorm:"primaryKey,autoIncrement"`
	SeriesID     uint
	Title        string
	Synopsis     string
	Cours        string
	Cast         string
	Production   string
	AnimeEpisode []AnimeEpisode `gorm:"constraint:OnUpdate:RESTRICT,OnDelete:CASCADE;"`
}

type AnimeEpisode struct {
	gorm.Model
	ID            uint `gorm:"primaryKey,autoIncrement"`
	AnimeSeasonID uint
	VideoID       uint
	Subtitle      string
	Number        string
	Video         Video `gorm:"constraint:OnUpdate:RESTRICT,OnDelete:CASCADE;"`
}

type Video struct {
	gorm.Model
	ID            uint `gorm:"primaryKey,autoIncrement"`
	BroadcastTime time.Time
	PlaybackTime  time.Time
}

type VideoLog struct {
	gorm.Model
	ID           uint `gorm:"primaryKey,autoIncrement"`
	VideoID      uint
	PlaybackTime time.Time
	Video        Video
}
