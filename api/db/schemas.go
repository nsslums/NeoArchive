package db

import (
	"time"

	"gorm.io/gorm"
)

type Series struct {
	gorm.Model
	ID          uint `gorm:"primaryKey,autoIncrement"`
	UserID      uint
	Title       string
	AnimeSeason []AnimeSeason //`gorm:"constraint:OnUpdate:RESTRICT,OnDelete:CASCADE;"`
	Tag         []Tag         `gorm:"many2many:series_tag;"`
}

type Tag struct {
	gorm.Model
	ID     uint `gorm:"primaryKey,autoIncrement"`
	UserID uint
	Name   string   `gorm:"uniqueIndex"`
	Series []Series `gorm:"many2many:series_tag;"`
}

type AnimeSeason struct {
	gorm.Model
	ID            uint `gorm:"primaryKey,autoIncrement"`
	UserID        uint
	SeriesID      uint
	DisplayNumber uint
	Title         string
	Synopsis      string
	Cours         string
	Production    string
	AnimeEpisode  []AnimeEpisode //`gorm:"constraint:OnUpdate:RESTRICT,OnDelete:CASCADE;"`
	Cast          []Cast         `gorm:"many2many:anime_season_cast;"`
}

type Cast struct {
	gorm.Model
	ID          uint `gorm:"primaryKey,autoIncrement"`
	UserID      uint
	Name        string        `gorm:"uniqueIndex"`
	AnimeSeason []AnimeSeason `gorm:"many2many:anime_season_cast;"`
}

type AnimeEpisode struct {
	gorm.Model
	ID            uint `gorm:"primaryKey,autoIncrement"`
	UserID        uint
	AnimeSeasonID uint
	DisplayNumber uint
	VideoID       uint
	Subtitle      string
	Number        string
	Video         Video //`gorm:"constraint:OnUpdate:RESTRICT,OnDelete:CASCADE;"`
}

type Video struct {
	gorm.Model
	ID            uint `gorm:"primaryKey,autoIncrement"`
	UserID        uint
	BroadcastTime time.Time
	Length        uint
}

type VideoLog struct {
	gorm.Model
	ID           uint `gorm:"primaryKey,autoIncrement"`
	UserID       uint
	VideoID      uint
	PlaybackTime time.Time
	Video        Video //`gorm:"constraint:OnUpdate:RESTRICT,OnDelete:CASCADE;"`
}
