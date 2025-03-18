import Hls from "hls.js";
import { useEffect, useRef, useState } from "react";
import screenfull from "screenfull";
import { FaPause, FaPlay, FaVolumeLow, FaVolumeXmark } from "react-icons/fa6";
import { MdOutlineSubtitles, MdOutlineSubtitlesOff } from "react-icons/md";
import { PiPictureInPicture, PiPictureInPictureFill } from "react-icons/pi";
import { IoReturnDownBack } from "react-icons/io5";
import {
  RiFullscreenExitLine,
  RiFullscreenFill,
  RiSettings3Fill,
} from "react-icons/ri";

interface PlayerStatus {
  isPlay: boolean;
  showSubtitle: boolean;
  volume: number;
  isMuted: boolean;
  duration: number;
  seek: number;
  load: number;
  isFullScreen: boolean;
  isPip: boolean;
  showController: boolean;
  showSettings: boolean;
  showAudioDes: boolean;
}

interface Qualitiy {
  level: number;
  name: string;
  codec?: string;
  bitrate?: number;
}

type PlayerProps = {
  src: string;
  className?: string;
  srcTitle?: string;
};

export default function Player({ src, className, srcTitle }: PlayerProps) {
  const videoRef = useRef<HTMLVideoElement>(null);
  const playerRoot = useRef(null);
  const hideControlsTimeout = useRef<NodeJS.Timeout | null>(null);

  const [playerStatus, setPlayerStatus] = useState<PlayerStatus>({
    isPlay: false,
    volume: 0.5,
    duration: 0,
    seek: 0,
    load: 0,
    isMuted: false,
    isFullScreen: false,
    isPip: false,
    showSubtitle: false,
    showController: false,
    showSettings: false,
    showAudioDes: false,
  });

  const [hlsInstance, setHlsInstance] = useState<Hls | null>(null);
  const [qualities, setQualities] = useState<Qualitiy[]>([]);
  const [selectedQuality, setSelectedQuality] = useState<number>(-1);

  useEffect(() => {
    const video = videoRef.current;
    if (!video) return;
    let hls: Hls | null = null;

    if (Hls.isSupported()) {
      hls = new Hls();
      hls.loadSource(src);
      hls.attachMedia(video);
      hls.on(Hls.Events.MANIFEST_PARSED, (_, data) => {
        setPlayerStatus((preState) => ({
          ...preState,
          duration: video.duration,
        }));
        const levels = hls?.levels.map(
          (level, index): Qualitiy => ({
            level: index,
            name: `${level.height}p`,
            codec: level.videoCodec || "",
            bitrate: level.bitrate,
          })
        );
        if (!levels) return;
        setQualities([{ level: -1, name: "auto" }, ...levels]);
      });

      hls.on(Hls.Events.LEVEL_SWITCHED, (_, data) => {
        console.log(`🎥 HLS Level Switched: ${data.level}`);
        console.log(
          `🔍 Current Resolution: ${hls?.levels[data.level]?.height}p / ${
            hls?.levels[data.level]?.codecs
          }`
        );
        console.log(`📶 Bitrate: ${hls?.levels[data.level]?.bitrate}bps`);
      });

      video
        .play()
        .then(() =>
          setPlayerStatus((preState) => ({
            ...preState,
            isPlay: true,
          }))
        )
        .catch(() => {
          setPlayerStatus((preState) => ({
            ...preState,
            isPlay: false,
          }));
        });

      hls.subtitleDisplay = playerStatus.showSubtitle;
    } else if (video.canPlayType("application/vnd.apple.mpegurl")) {
      video.src = src;
    }

    const savedVolume = localStorage.getItem("playerVolume");
    if (savedVolume) {
      setPlayerStatus((preState) => ({
        ...preState,
        volume: parseFloat(savedVolume),
      }));
      video.volume = parseFloat(savedVolume);
    } else {
      video.volume = playerStatus.volume;
    }

    const updateTime = () =>
      setPlayerStatus((preState) => ({
        ...preState,
        seek: video.currentTime,
      }));
    const setVideoDuration = () =>
      setPlayerStatus((preState) => ({
        ...preState,
        duration: video.duration,
      }));

    const updateBuffered = () => {
      if (video.buffered.length > 0) {
        setPlayerStatus((preState) => ({
          ...preState,
          load: video.buffered.end(video.buffered.length - 1),
        }));
      }
    };

    const toggleSubtitleKey = () => {
      if (!hls) return;
      if (hls.allSubtitleTracks.length === 0) return;
      hls.subtitleDisplay = !hls.subtitleDisplay;
      setPlayerStatus((preState) => ({
        ...preState,
        showSubtitle: hls.subtitleDisplay,
      }));
    };

    const handleKeyDown = (e: KeyboardEvent) => {
      if (!video) return;
      switch (e.key) {
        case " ":
          e.preventDefault();
          togglePlay();
          break;
        case "ArrowRight":
          e.preventDefault();
          move(5);
          break;
        case "ArrowLeft":
          e.preventDefault();
          move(-5);
          break;
        case "ArrowUp":
          e.preventDefault();
          changeVolumeDelta(0.05);
          break;
        case "ArrowDown":
          e.preventDefault();
          changeVolumeDelta(-0.05);
          break;
        case "m":
          e.preventDefault();
          toggleMute();
          break;
        case "f":
          e.preventDefault();
          handleFullScreen();
          break;
        case "c":
          e.preventDefault();
          toggleSubtitleKey();
          break;
      }
    };

    const onExitPip = () => {
      setPlayerStatus((preState) => ({
        ...preState,
        isPip: false,
      }));
    };

    const fullScreenChange = () => {
      if (document.fullscreenElement) {
        setPlayerStatus((preState) => ({
          ...preState,
          isFullScreen: true,
        }));
      } else {
        setPlayerStatus((preState) => ({
          ...preState,
          isFullScreen: false,
        }));
      }
    };

    video.addEventListener("timeupdate", updateTime);
    video.addEventListener("loadedmetadata", setVideoDuration);
    video.addEventListener("progress", updateBuffered);
    document.addEventListener("keydown", handleKeyDown);
    document.addEventListener("leavepictureinpicture", onExitPip);
    document.addEventListener("fullscreenchange", fullScreenChange);

    setHlsInstance(hls);

    return () => {
      video.removeEventListener("timeupdate", updateTime);
      video.removeEventListener("progress", updateBuffered);
      video.removeEventListener("loadedmetadata", setVideoDuration);
      document.removeEventListener("keydown", handleKeyDown);
      document.removeEventListener("leavepictureinpicture", onExitPip);
      document.removeEventListener("fullscreenchange", fullScreenChange);

      if (hls) {
        hls.destroy();
      }
    };
  }, [src]);

  const showControlsTemporarily = () => {
    setPlayerStatus((preState) => ({
      ...preState,
      showController: true,
    }));

    if (hideControlsTimeout.current) clearTimeout(hideControlsTimeout.current);
    hideControlsTimeout.current = setTimeout(
      () =>
        setPlayerStatus((preState) => ({
          ...preState,
          showController: false,
        })),
      3000
    );
  };

  const togglePlay = async () => {
    const video = videoRef.current;
    if (!video) return;

    if (video.paused) {
      video
        .play()
        .then(() =>
          setPlayerStatus((preState) => ({
            ...preState,
            isPlay: true,
          }))
        )
        .catch((e) => console.log(e));
    } else {
      video.pause();
      setPlayerStatus((preState) => ({
        ...preState,
        isPlay: false,
      }));
    }
    showControlsTemporarily();
  };

  const handleVolumeChange = (value: number) => {
    const video = videoRef.current;
    if (!video) return;

    video.volume = value;
    setPlayerStatus((preState) => ({
      ...preState,
      volume: value,
    }));
    localStorage.setItem("playerVolume", value.toString());
    showControlsTemporarily();
  };

  const changeVolumeDelta = (delta: number) => {
    const video = videoRef.current;
    if (!video) return;

    const newVol = video.volume + delta;

    if (newVol < 0) video.volume = 0;
    else if (1 < newVol) video.volume = 1;
    else video.volume += delta;

    setPlayerStatus((preState) => ({
      ...preState,
      volume: video.volume,
    }));
    localStorage.setItem("playerVolume", video.volume.toString());
    showControlsTemporarily();
  };

  const toggleMute = () => {
    const video = videoRef.current;
    if (!video) return;

    video.muted = !video.muted;
    setPlayerStatus((preState) => ({
      ...preState,
      isMuted: video.muted,
    }));
    showControlsTemporarily();
  };

  const handleSeek = (event: React.ChangeEvent<HTMLInputElement>) => {
    const video = videoRef.current;
    if (!video) return;

    const time = parseFloat(event.target.value);
    video.currentTime = time;
    setPlayerStatus((preState) => ({
      ...preState,
      seek: time,
    }));
    showControlsTemporarily();
  };

  const move = (second: number) => {
    const video = videoRef.current;
    if (!video) return;

    video.currentTime += second;
    showControlsTemporarily();
  };

  const handleFullScreen = () => {
    if (!playerRoot.current) return;
    if (!screenfull.isEnabled) return;

    screenfull.toggle(playerRoot.current);
    showControlsTemporarily();
  };

  const toggleSubtitle = () => {
    if (!hlsInstance) return;
    if (hlsInstance.allSubtitleTracks.length === 0) return;
    hlsInstance.subtitleDisplay = !hlsInstance.subtitleDisplay;
    setPlayerStatus((preState) => ({
      ...preState,
      showSubtitle: hlsInstance.subtitleDisplay,
    }));
  };

  const formatTime = (seconds: number) => {
    if (isNaN(seconds) || seconds <= 0) return "0:00";
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${minutes}:${secs.toString().padStart(2, "0")}`;
  };

  const handleChangeLevel = (level: number) => {
    if (hlsInstance) {
      console.log(level);
      hlsInstance.nextLevel = level;
      setSelectedQuality(level);
      setPlayerStatus((preState) => ({
        ...preState,
        showSettings: !preState.showSettings,
      }));
    }
  };

  const toggleSettings = () => {
    setPlayerStatus((preState) => ({
      ...preState,
      showSettings: !preState.showSettings,
    }));
  };

  const handlePip = () => {
    if (document.pictureInPictureElement) {
      document.exitPictureInPicture();
    } else {
      videoRef.current?.requestPictureInPicture();
    }
    setPlayerStatus((preState) => ({
      ...preState,
      isPip: !document.pictureInPictureElement,
    }));
  };

  return (
    <div className={className}>
      <div
        className={`relative w-full text-white bg-black overflow-hidden ${
          playerStatus.showController ? "" : "cursor-none"
        } ${playerStatus.isFullScreen ? "rounded-none" : "rounded-xl"}`}
        ref={playerRoot}
        onMouseEnter={() =>
          setPlayerStatus((preState) => ({
            ...preState,
            showController: true,
          }))
        }
        onMouseMove={showControlsTemporarily}
        onMouseLeave={() =>
          setPlayerStatus((preState) => ({
            ...preState,
            showController: false,
          }))
        }
        tabIndex={0}
      >
        <div
          className="relative z-0 w-full"
          onClick={() => {
            togglePlay();
          }}
        >
          <video ref={videoRef} width="100%" className="aspect-video" />
        </div>
        {playerStatus.showSettings ? (
          <div
            className="absolute top-0 left-0 w-full h-full"
            onClick={toggleSettings}
          />
        ) : null}

        <div
          className={`w-full h-full duration-200 ${
            playerStatus.showController ? "opacity-100" : "opacity-0"
          }`}
        >
          {playerStatus.isFullScreen ? (
            <div className="text-3xl absolute top-0 left-0 mt-3 ml-4 tracking-wide">
              {srcTitle}
            </div>
          ) : null}

          <div
            className={`w-full absolute bottom-0 text-xs z-10 duration-200 ${
              playerStatus.showController
                ? "opacity-100"
                : "opacity-0 pointer-events-none"
            }`}
          >
            <div
              className={`px-4 mb-1 duration-200 absolute right-0 bottom-[45px] ${
                playerStatus.showSettings
                  ? "opacity-100"
                  : "opacity-0 pointer-events-none"
              }`}
            >
              <ul className="max-h-40 overflow-y-scroll inline-block bg-black rounded-lg p-1 text-sm space-y-2">
                {qualities.map((q) => (
                  <li
                    key={q.level}
                    value={q.level}
                    onClick={() => handleChangeLevel(q.level)}
                    className={`cursor-pointer px-2 pr-4 py-1 rounded-sm hover:bg-gray-600  ${
                      selectedQuality === q.level ? "bg-red-300" : ""
                    }`}
                  >
                    {q.name}
                  </li>
                ))}
              </ul>
            </div>
            <div className="backdrop-blur-sm px-4 pb-3">
              <div className="w-full h-4 relative">
                <span className="bg-gray-500 h-1 block w-full absolute top-0 rounded-sm"></span>
                <span
                  className="bg-gray-300 h-1 block absolute top-0 rounded-sm w-full"
                  style={{
                    width: `${
                      (playerStatus.load / playerStatus.duration) * 100
                    }%`,
                  }}
                ></span>
                <span
                  className="bg-red-300 h-1 block absolute top-0 rounded-sm w-full z-10"
                  style={{
                    width: `${
                      (playerStatus.seek / playerStatus.duration) * 100
                    }%`,
                  }}
                ></span>
                <span
                  className="bg-red-300 size-3 rounded-full block absolute -top-1 left-0 z-10"
                  style={{
                    left: `${
                      (playerStatus.seek / playerStatus.duration) * 100 - 0.5
                    }%`,
                  }}
                ></span>
                <input
                  type="range"
                  min={0}
                  max={playerStatus.duration || 0}
                  step={0.1}
                  value={playerStatus.seek || 0}
                  onChange={handleSeek}
                  disabled={playerStatus.duration == 0}
                  className="h-1 block absolute top-0 rounded-sm w-full opacity-0 z-20 cursor-pointer"
                />
              </div>

              <div className="flex justify-between">
                <div className="flex gap-3 items-center">
                  <button
                    onClick={() => {
                      togglePlay();
                    }}
                  >
                    {playerStatus.isPlay ? (
                      <FaPause size={18} />
                    ) : (
                      <FaPlay size={18} />
                    )}
                  </button>
                  <div
                    className="flex gap-2 items-center"
                    onMouseEnter={() =>
                      setPlayerStatus((preState) => ({
                        ...preState,
                        showAudioDes: true,
                      }))
                    }
                    onMouseLeave={() =>
                      setPlayerStatus((preState) => ({
                        ...preState,
                        showAudioDes: false,
                      }))
                    }
                  >
                    <button onClick={toggleMute}>
                      {playerStatus.isMuted ? (
                        <FaVolumeXmark size={18} />
                      ) : (
                        <FaVolumeLow size={18} />
                      )}
                    </button>
                    <div
                      className={`relative duration-200 h-4 overflow-hidden ${
                        playerStatus.showAudioDes ? "w-20" : "w-0"
                      }`}
                    >
                      <span className="bg-gray-300 h-1 block absolute top-1/2 -translate-y-1/2 left-0 rounded-sm w-full"></span>
                      <span
                        className="bg-red-300 h-1 block absolute top-1/2 -translate-y-1/2 left-0 rounded-sm w-1/2 z-10"
                        style={{
                          width: `${playerStatus.volume * 100}%`,
                        }}
                      ></span>
                      <span
                        className="bg-red-300 size-2 rounded-full block absolute top-1/2 -translate-y-1/2 left-0 z-10"
                        style={{
                          left: `${playerStatus.volume * 100 - 2.5}%`,
                        }}
                      ></span>
                      <input
                        type="range"
                        min={0}
                        max={1}
                        step={0.01}
                        value={playerStatus.volume}
                        onChange={(e) => {
                          handleVolumeChange(parseFloat(e.target.value));
                        }}
                        className="opacity-0 absolute h-1 w-full top-1/2 -translate-y-1/2 left-0 z-20 cursor-pointer"
                      />
                    </div>
                  </div>
                  <div>
                    {formatTime(playerStatus.seek)}
                    {" / "}
                    {formatTime(playerStatus.duration)}
                  </div>
                </div>
                <div className="flex gap-3 items-center">
                  <button onClick={toggleSubtitle}>
                    {playerStatus.showSubtitle ? (
                      <MdOutlineSubtitles size={18} />
                    ) : (
                      <MdOutlineSubtitlesOff size={18} />
                    )}
                  </button>
                  <button onClick={toggleSettings}>
                    <RiSettings3Fill size={18} />
                  </button>
                  <button onClick={handlePip}>
                    {playerStatus.isPip ? (
                      <PiPictureInPictureFill size={18} />
                    ) : (
                      <PiPictureInPicture size={18} />
                    )}
                  </button>
                  <button
                    onClick={() => {
                      move(-10);
                    }}
                  >
                    <IoReturnDownBack size={18} />
                  </button>
                  <button
                    onClick={() => {
                      move(10);
                    }}
                    className="rotate-180"
                  >
                    <IoReturnDownBack size={18} />
                  </button>
                  <button
                    onClick={() => {
                      handleFullScreen();
                    }}
                  >
                    {playerStatus.isFullScreen ? (
                      <RiFullscreenExitLine size={18} />
                    ) : (
                      <RiFullscreenFill size={18} />
                    )}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
