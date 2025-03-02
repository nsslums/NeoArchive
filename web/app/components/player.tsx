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
  isControllerShow: boolean;
  showSettings: boolean;
  showAudioDes: boolean;
}

interface Qualitiy {
  level: number;
  name: string;
  codec?: string;
  bitrate?: number;
}

type PropsType = {
  src: string;
};

export default function Player({ src }: PropsType) {
  const videoRef = useRef<HTMLVideoElement>(null);
  const playerRoot = useRef(null);
  const hideControlsTimeout = useRef<NodeJS.Timeout | null>(null);

  const [playerStatus, setPlayerStatus] = useState<PlayerStatus>({
    isPlay: false,
    showSubtitle: false,
    volume: 0.5,
    isMuted: false,
    duration: 1,
    seek: 0,
    load: 0,
    isFullScreen: false,
    isPip: false,
    isControllerShow: true,
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
        .catch((e) =>
          setPlayerStatus((preState) => ({
            ...preState,
            isPlay: false,
          }))
        );

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
      }
    };

    const onExitPip = () => {
      setPlayerStatus((preState) => ({
        ...preState,
        isPip: false,
      }));
    };

    video.addEventListener("timeupdate", updateTime);
    video.addEventListener("loadedmetadata", setVideoDuration);
    video.addEventListener("progress", updateBuffered);
    document.addEventListener("keydown", handleKeyDown);
    document.addEventListener("leavepictureinpicture", onExitPip);

    setHlsInstance(hls);

    return () => {
      video.removeEventListener("timeupdate", updateTime);
      video.removeEventListener("progress", updateBuffered);
      video.removeEventListener("loadedmetadata", setVideoDuration);
      document.removeEventListener("keydown", handleKeyDown);
      document.removeEventListener("leavepictureinpicture", onExitPip);

      if (hls) {
        hls.destroy();
      }
    };
  }, [src]);

  const showControlsTemporarily = () => {
    setPlayerStatus((preState) => ({
      ...preState,
      isControllerShow: true,
    }));

    if (hideControlsTimeout.current) clearTimeout(hideControlsTimeout.current);
    hideControlsTimeout.current = setTimeout(
      () =>
        setPlayerStatus((preState) => ({
          ...preState,
          isControllerShow: false,
        })),
      3000
    );
  };

  const togglePlay = () => {
    const video = videoRef.current;
    if (!video) return;

    if (video.paused) {
      video.play();
    } else {
      video.pause();
    }
    setPlayerStatus((preState) => ({
      ...preState,
      isPlay: !video.paused,
    }));
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
    console.log(!hlsInstance.subtitleDisplay, !playerStatus.showSubtitle);
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
    <div
      className={`relative text-white bg-black ${
        playerStatus.isControllerShow ? "" : "cursor-none"
      }`}
      ref={playerRoot}
      onMouseEnter={() =>
        setPlayerStatus((preState) => ({
          ...preState,
          isControllerShow: true,
        }))
      }
      onMouseMove={showControlsTemporarily}
      onMouseLeave={() =>
        setPlayerStatus((preState) => ({
          ...preState,
          isControllerShow: false,
        }))
      }
      tabIndex={0}
    >
      <div
        className="relative z-0"
        onClick={() => {
          togglePlay();
        }}
      >
        <video ref={videoRef} width="100%" />
      </div>
      <div
        className={`w-full absolute bottom-0 text-xs z-10 ${
          playerStatus.isControllerShow
            ? "opacity-100"
            : "opacity-0 pointer-events-none"
        } duration-200`}
      >
        <div
          className={`px-4 mb-1 duration-200 absolute right-0 bottom-[45px] ${
            playerStatus.showSettings
              ? "opacity-100"
              : "opacity-0 pointer-events-none"
          }`}
        >
          <ul className="h-40 overflow-y-scroll inline-block bg-black rounded-lg p-1 text-sm">
            {qualities.map((q) => (
              <li
                key={q.level}
                value={q.level}
                onClick={() => handleChangeLevel(q.level)}
                className={`cursor-pointer my-1 hover:bg-gray-200 rounded-sm ${
                  selectedQuality === q.level ? "bg-blue-500" : ""
                }`}
              >
                {q.name} / {q.codec}
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
                width: `${(playerStatus.load / playerStatus.duration) * 100}%`,
              }}
            ></span>
            <span
              className="bg-red-300 h-1 block absolute top-0 rounded-sm w-full z-10"
              style={{
                width: `${(playerStatus.seek / playerStatus.duration) * 100}%`,
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
              className="h-1 block absolute top-0 rounded-sm w-full opacity-0 z-20"
            />
          </div>

          <div className="flex justify-between">
            <div className="flex gap-2 items-center">
              <button
                onClick={() => {
                  togglePlay();
                }}
              >
                {playerStatus.isPlay ? <FaPlay /> : <FaPause />}
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
                  {playerStatus.isMuted ? <FaVolumeXmark /> : <FaVolumeLow />}
                </button>
                <div
                  className={`relative duration-200 h-4 ${
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
                  <input
                    type="range"
                    min={0}
                    max={1}
                    step={0.01}
                    value={playerStatus.volume}
                    onChange={(e) => {
                      handleVolumeChange(parseFloat(e.target.value));
                    }}
                    className="opacity-0 absolute h-1 w-full top-1/2 -translate-y-1/2 left-0 z-20"
                  />
                </div>
              </div>
              <div>
                {formatTime(playerStatus.seek)}
                {" / "}
                {formatTime(playerStatus.duration)}
              </div>
            </div>
            <div className="flex gap-2 items-center">
              <button onClick={toggleSubtitle}>
                {playerStatus.showSubtitle ? (
                  <MdOutlineSubtitles />
                ) : (
                  <MdOutlineSubtitlesOff />
                )}
              </button>
              <button onClick={toggleSettings}>
                <RiSettings3Fill />
              </button>
              <button onClick={handlePip}>
                {playerStatus.isPip ? (
                  <PiPictureInPictureFill />
                ) : (
                  <PiPictureInPicture />
                )}
              </button>
              <button
                onClick={() => {
                  move(-10);
                }}
              >
                <IoReturnDownBack />
              </button>
              <button
                onClick={() => {
                  move(10);
                }}
              >
                <IoReturnDownBack rotate={180} />
              </button>
              <button
                onClick={() => {
                  handleFullScreen();
                }}
              >
                {playerStatus.isFullScreen ? (
                  <RiFullscreenExitLine />
                ) : (
                  <RiFullscreenFill />
                )}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
