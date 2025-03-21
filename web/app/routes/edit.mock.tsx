import { LoaderFunctionArgs } from "@remix-run/node";
import { data, Outlet, useLoaderData } from "@remix-run/react";
import type { paths } from "api/schema";
import createClient from "openapi-fetch";
import { useState } from "react";
import {
  arrayMove,
  SortableContext,
  verticalListSortingStrategy,
} from "@dnd-kit/sortable";
import { Droppable } from "~/components/sort/Droppable";
import {
  Active,
  DndContext,
  DragEndEvent,
  DragOverEvent,
  DragOverlay,
  DragStartEvent,
  MouseSensor,
  Over,
  pointerWithin,
  UniqueIdentifier,
  useSensor,
  useSensors,
} from "@dnd-kit/core";
import { Sortable } from "~/components/sort/Sortable";
import { SortableDrop } from "~/components/sort/SortableDrop";
import { SeriesAccordion } from "~/components/edit/seriesAccordion";
import { MenuSeasonItem } from "~/components/sort/MenuSeasonItem";
import { Card } from "~/components/ui/card";
import { Skeleton } from "~/components/ui/skeleton";
import { EpisodeItem } from "~/components/sort/EpisodeItem";
import { ScrollArea } from "~/components/ui/scroll-area";

type TagDetail = {
  id: number;
  name: string;
};
type CastDetail = {
  id: number;
  name: string;
};
export type EpisodeDetail = {
  id: number;
  season_id?: number;
  video_id?: number;
  display_number?: number;
  number?: string;
  subtitle: string;
};
type SeasonDetail = {
  id: number;
  series_id?: number;
  casts?: CastDetail[];
  display_number?: number;
  title: string;
  synopsis?: string;
  cours?: string;
  production?: string;
  episodes: EpisodeDetail[];
};
export type SeriesDetail = {
  id: number;
  title: string;
  tags?: TagDetail[];
  season: SeasonDetail[];
};
type AnimeDetail = {
  id: string;
  name: string;
  series: SeriesDetail[];
};
type AnimeIndexes = {
  series: number;
  season: number;
};

export const loader = async ({ params }: LoaderFunctionArgs) => {
  const client = createClient<paths>({ baseUrl: process.env.API_URL });
  try {
    // const { data: season } = await client.GET("/anime/season/{id}", {
    //   params: {
    //     path: { id: Number(params.season_id) },
    //   },
    // });
    // if (!season) {
    //   return;
    // }
    // const { data: series, error } = await client.GET("/series/{id}", {
    //   params: {
    //     path: { id: season.series_id },
    //   },
    // });
    // const { data: season_list } = await client.GET(
    //   "/anime/season_list/{series_id}",
    //   {
    //     params: {
    //       path: { series_id: season.series_id },
    //     },
    //   }
    // );
    // if (!season_list) {
    //   return;
    // }
    // const { data: episode_list } = await client.GET(
    //   "/anime/episode_list/{season_id}",
    //   {
    //     params: {
    //       path: { season_id: season.id },
    //     },
    //   }
    // );
  } catch (error) {
    console.log("deta fech", error);
  }

  const sampleAnimeData: AnimeDetail = {
    id: "animes",
    name: "Anime",
    series: [
      {
        id: 10,
        title: "Re:ゼロから始める異世界生活",
        season: [
          {
            id: 100,
            title: "Re:ゼロから始める異世界生活 1rd season",
            episodes: [
              {
                id: 101,
                number: "1",
                subtitle: "Episode 1-1",
              },
              {
                id: 102,
                number: "2",
                subtitle: "Episode 1-2",
              },
              {
                id: 103,
                number: "3",
                subtitle: "Episode 1-3",
              },
              {
                id: 104,
                number: "4",
                subtitle: "Episode 1-3",
              },
              {
                id: 105,
                number: "5",
                subtitle: "Episode 1-3",
              },
              {
                id: 106,
                number: "6",
                subtitle: "Episode 1-3",
              },
            ],
            series_id: 0,
            casts: [
              { id: 3210, name: "test" },
              { id: 3211, name: "test2" },
            ],
            display_number: 0,
            synopsis:
              "コンビニからの帰り道、突如として異世界へと召喚されてしまった少年、菜月昴。目の前に広がるファンタジーな異世界に目を輝かせるスバルだったが、自分を召喚したであろう美少女の姿はどこにも見当たらない。やがて右も左もわからない状況にスバルは頭をかかえてしまう。さらに強制イベントと言わんばかりにチンピラに絡まれ、異世界に招かれた人間が超常の力を発揮するといったお約束の展開もなく、容赦なく叩きのめされるスバル。そんなスバルの前に一人の少女が現れ……。",
            cours: "summer",
            production: "production ooooo",
          },
          {
            id: 200,
            title: "Re:ゼロから始める異世界生活 2rd season",
            episodes: [
              {
                id: 201,
                subtitle: "Episode 2-1",
              },
              {
                id: 202,
                subtitle: "Episode 2-2",
              },
            ],
            synopsis:
              '《無力な少年が手にしたのは、死して時間を巻き戻す"死に戻り"の力》コンビニからの帰り道、突如として異世界へと召喚されてしまった少年・菜月昴。頼れるものなど何一つない異世界で、無力な少年が手にした唯一の力……それは死して時間を巻き戻す《死に戻り》の力だった。幾多の死を繰り返しながら、辛い決別を乗り越え、ようやく訪れた最愛の少女との再会も束の間、少年を襲う無慈悲な現実と想像を絶する危機。大切な人たちを守るため、そして確かにあったかけがえのない時間を取り戻すため、少年は再び絶望に抗い、過酷な運命に立ち向かっていく。',
          },
          {
            id: 600,
            title: "Re:ゼロから始める異世界生活 3rd season",
            episodes: [
              {
                id: 601,
                subtitle: "Episode 2-1",
              },
              {
                id: 602,
                subtitle: "Episode 2-2",
              },
            ],
            synopsis:
              "襲い来るエルザたちの猛攻を退け、大兎との戦いでベアトリスとの契約を果たした「聖域」の解放から1年が過ぎた。王選に臨むエミリア陣営は一致団結、充実した日々を送っていたナツキ・スバルだったが、平穏は使者によって届けられた一枚の書状によって終わりを告げる。それは王選候補者の一人、アナスタシアがエミリアへ宛てたルグニカの五大都市に数えられる水門都市プリステラへの招待状だった。招待を受け、プリステラへ向かうスバルたち一行を待っていたのは様々な再会。一つは意外な、一つは意図せぬ、そして一つは来るべき。水面下で蠢く悪意の胎動と降りかかる未曾有の危機。少年は再び過酷な運命に立ち向かう。",
          },
        ],
      },
      {
        id: 20,
        title:
          "ギルドの受付嬢ですが、残業は嫌なのでボスをソロ討伐しようと思います",
        season: [
          {
            id: 300,
            title:
              "ギルドの受付嬢ですが、残業は嫌なのでボスをソロ討伐しようと思います",
            episodes: [
              {
                id: 301,
                subtitle: "Episode 1-1",
              },
              {
                id: 302,
                subtitle: "Episode 1-2",
              },
              {
                id: 303,
                subtitle: "Episode 1-3",
              },
            ],
            synopsis:
              "〈ギルドの受付嬢〉。業務内容は絶対安全。公務だから立場も安定。可愛い制服に身を包み、カウンター越しに笑顔で冒険者たちをご案内。受付時間が終わったら、のんびりと事務作業を済ませて定時に帰宅。愛しの我が家でくつろいで、さあ、明日も元気に働こうーー。アリナ・クローバーは、そんな理想の職業に就いたはずだった。しかし。その実態は、理想とは程遠かったーひとたびダンジョンの攻略が滞れば、カウンターは大混雑。めんどくさい対応を求める冒険者もちらほら。顔で笑って心で泣いて、厄介な顧客をやり過ごしても、今度は大量の書類仕事が待っている。やる気は残ってないけれど、明日に回せばなおしんどい。おかげで来る日も来る日も残業地獄…。ああ、もう我慢の限界!!アリナが不満を爆発させると、隠し持った一面が顔を出す。チームで挑むことすら危険なダンジョンにソロで乗り込み、銀に輝く大鎚【ウォーハンマー】で、強大なボスを叩き伏せる――。何を隠そう彼女こそ、正体不明、神出鬼没、街で噂の凄腕冒険者〈処刑人〉その人だったのだ!!でも、そのことは絶対に隠し通さなければならない。なぜなら受付嬢は副業禁止で、バレたら即刻クビだから…。アリナの平穏な暮らしは、守られるのか⁉第27回電撃小説大賞《金賞》受賞作、待望のTVアニメ化！",
          },
        ],
      },
    ],
  };

  return sampleAnimeData;
};

export default function SeasonEdit() {
  const loadAnimeData = useLoaderData<typeof loader>();

  const [animeData, setanimeData] = useState<AnimeDetail>(loadAnimeData);
  const [activeId, setActiveId] = useState<UniqueIdentifier | null>(null);
  const [editAnimeId, seteditAnimeId] = useState<AnimeIndexes>({
    series: animeData.series[0]?.id || 0,
    season: animeData.series[0]?.season[0]?.id || 0,
  });
  const [isSelectSeason, setIsSelectSeason] = useState(false);

  const sensors = useSensors(
    useSensor(MouseSensor, { activationConstraint: { distance: 5 } })
  );

  function habdleDragStart(event: DragStartEvent): void {
    const { active } = event;
    if (!active) return;
    setActiveId(active.id);

    setIsSelectSeason(
      animeData.series.some((series) =>
        series.season.some((season) => season.id == active.id)
      )
    );
  }

  // 別のシリーズにシーズンを移動
  function handleDragOver(event: DragOverEvent): void {
    const data = getData(event, animeData);
    if (!data) return;

    const { from, to } = data;
    if (from.containerId === to.containerId) return;

    const fromList = animeData.series.find(
      (series) => series.id.toString() == from.containerId
    );
    const toList = animeData.series.find(
      (series) => series.id.toString() == to.containerId
    );

    if (!fromList || !toList) return;

    const moveSeason = fromList.season.find(
      (season) => season.id === from.items[from.index]
    );
    if (!moveSeason) return;

    const newFromSeason = fromList.season.filter(
      (season) => season.id !== moveSeason.id
    );

    const newToSeason = [
      ...toList.season.slice(0, to.index),
      moveSeason,
      ...toList.season.slice(to.index),
    ];

    setanimeData((prevData) => {
      return {
        ...prevData,
        series: animeData.series.map((series) => {
          if (series.id.toString() === from.containerId)
            return { ...series, season: newFromSeason };

          if (series.id.toString() === to.containerId)
            return { ...series, season: newToSeason };

          return series;
        }),
      };
    });
  }

  // 同じコンテナ内の移動 & 別のシーズンにエピソード移動
  function handleDragEnd(event: DragEndEvent): void {
    setActiveId(null);
    const data = getData(event, animeData);
    if (!data) return;

    const { from, to } = data;

    // 別のシーズンにエピソード移動
    if (from.containerId !== to.containerId) {
      const FromList = animeData.series
        .flatMap((series) => series.season)
        .find((series) => series.id.toString() == from.containerId);
      if (!FromList) return;

      const toList = animeData.series
        .flatMap((series) => series.season)
        .find((series) => series.id.toString() + "-menu" == to.containerId);
      if (!toList) return;

      if (
        toList.episodes.some((ep) => ep.id.toString() == from.items[from.index])
      )
        return;

      const moveEpisode = FromList.episodes[from.index];
      if (!moveEpisode) return;

      const newFromEpisode = FromList.episodes.filter(
        (episodes) => episodes.id !== moveEpisode.id
      );

      const newToEpisode = [
        ...toList.episodes.slice(0, to.index),
        moveEpisode,
        ...toList.episodes.slice(to.index),
      ];

      setanimeData((prevData) => {
        return {
          ...prevData,
          series: prevData.series.map((series) => ({
            ...series,
            season: series.season.map((season) => {
              if (season.id.toString() === from.containerId)
                return { ...season, episodes: newFromEpisode };

              if (season.id.toString() + "-menu" === to.containerId)
                return { ...season, episodes: newToEpisode };

              return season;
            }),
          })),
        };
      });
      return;
    }

    const season = animeData.series
      .flatMap((series) => series.season)
      .find((season) => season.id.toString() == from.containerId);

    if (season) {
      // エピソードの並び替え
      const newEpisondes = arrayMove(season.episodes, from.index, to.index);

      setanimeData((prevData) => {
        return {
          ...prevData,
          series: prevData.series.map((series) => ({
            ...series,
            season: series.season.map((season) => ({
              ...season,
              episodes:
                season.id.toString() === from.containerId
                  ? newEpisondes
                  : season.episodes,
            })),
          })),
        };
      });
    } else {
      // シーズンの並び替え
      const series = animeData.series.find(
        (series) => series.id.toString() == from.containerId
      );
      if (!series) return;

      const newSeason = arrayMove(series.season, from.index, to.index);

      setanimeData((prevData) => {
        return {
          ...prevData,
          series: prevData.series.map((series) => ({
            ...series,
            season:
              series.id.toString() === from.containerId
                ? newSeason
                : series.season,
          })),
        };
      });
    }
  }

  function getData(
    event: { active: Active; over: Over | null },
    animeData: AnimeDetail
  ) {
    const { active, over } = event;
    if (!active || !over) return;
    if (active.id === over.id) return;
    const fromData = active.data.current?.sortable;
    if (!fromData) return;
    const toData = over.data.current?.sortable;
    const toDataNotSortable = {
      containerId: over.id,
      index: NaN,
      items: NaN,
    };
    return {
      from: fromData,
      to: toData ?? toDataNotSortable,
    };
  }

  function handleAddColumn(): void {
    const currentData = animeData.series;
    const d = new Date();
    const id = d.getUTCMilliseconds();

    const newData = {
      id,
      title: "List " + id,
      season: [],
    };
    currentData.push(newData);
    setanimeData({ ...animeData, series: currentData });
  }

  const nowEditSeason = animeData.series
    .flatMap((series) => series.season)
    .find((season) => season.id == editAnimeId.season) || {
    id: "none",
    title: "none anime data",
    episodes: [],
    series_id: 0,
    casts: [],
    display_number: 0,
    synopsis: "gaiyou gaiyou",
    cours: "aki-",
    production: "string",
  };

  const SortableComponent = isSelectSeason ? Sortable : SortableDrop;

  const SeasonList = (series: SeriesDetail) => (
    <div className="flex flex-col gap-1 p-2">
      {series.season.map((season) => (
        <SortableComponent
          key={season.id}
          id={season.id}
          onClick={() =>
            seteditAnimeId({
              series: series.id,
              season: season.id,
            })
          }
          className={`rounded-lg hover:bg-zinc-100 ${
            editAnimeId.season == season.id ? "bg-zinc-100" : ""
          }`}
        >
          <MenuSeasonItem value={season.title} />
        </SortableComponent>
      ))}
    </div>
  );

  return (
    <div>
      <div className="flex gap-12">
        <DndContext
          sensors={sensors}
          onDragStart={habdleDragStart}
          onDragOver={handleDragOver}
          onDragEnd={handleDragEnd}
          collisionDetection={pointerWithin}
          id={animeData.id}
        >
          <div className="flex flex-col gap-4 w-[400px] h-[calc(100vh-(57px+62px+2px))] pb-4 sticky top-0 overflow-auto no-scrollbar">
            {animeData.series.map((series) => (
              <SeriesAccordion key={series.id} data={series}>
                <SortableContext
                  items={series.season}
                  key={series.id}
                  id={series.id.toString()}
                  strategy={verticalListSortingStrategy}
                >
                  {isSelectSeason ? (
                    <Droppable key={series.id} id={series.id.toString()}>
                      <SeasonList
                        id={series.id}
                        title={series.title}
                        season={series.season}
                      />
                    </Droppable>
                  ) : (
                    <SeasonList
                      id={series.id}
                      title={series.title}
                      season={series.season}
                    />
                  )}
                </SortableContext>
              </SeriesAccordion>
            ))}
            {animeData.series.map((series) => (
              <SeriesAccordion key={series.id} data={series}>
                <SortableContext
                  items={series.season}
                  key={series.id}
                  id={series.id.toString()}
                  strategy={verticalListSortingStrategy}
                >
                  {isSelectSeason ? (
                    <Droppable key={series.id} id={series.id.toString()}>
                      <SeasonList
                        id={series.id}
                        title={series.title}
                        season={series.season}
                      />
                    </Droppable>
                  ) : (
                    <SeasonList
                      id={series.id}
                      title={series.title}
                      season={series.season}
                    />
                  )}
                </SortableContext>
              </SeriesAccordion>
            ))}
            {animeData.series.map((series) => (
              <SeriesAccordion key={series.id} data={series}>
                <SortableContext
                  items={series.season}
                  key={series.id}
                  id={series.id.toString()}
                  strategy={verticalListSortingStrategy}
                >
                  {isSelectSeason ? (
                    <Droppable key={series.id} id={series.id.toString()}>
                      <SeasonList
                        id={series.id}
                        title={series.title}
                        season={series.season}
                      />
                    </Droppable>
                  ) : (
                    <SeasonList
                      id={series.id}
                      title={series.title}
                      season={series.season}
                    />
                  )}
                </SortableContext>
              </SeriesAccordion>
            ))}
          </div>
          <div className="flex-1 space-y-4">
            <Card className="p-3 w-full">
              <div className="flex flex-row gap-4">
                <Skeleton className="w-56 aspect-video" />
                <div className="">
                  <h2 className="text-2xl">{nowEditSeason.title}</h2>
                  <div className="flex flex-row gap-2">
                    <p>oooo年 {nowEditSeason.cours}</p>
                    <p>制作：{nowEditSeason.production}</p>
                  </div>
                  <div className="flex flex-row gap-2">
                    出演：
                    {nowEditSeason.casts?.map((cast) => (
                      <p key={cast.id}>{cast.name}</p>
                    ))}
                  </div>
                </div>
              </div>
              <div className="mt-2 max-h-14 w-full overflow-hidden overflow-ellipsis">
                {nowEditSeason.synopsis}
              </div>
            </Card>
            <SortableContext
              items={nowEditSeason.episodes}
              key={nowEditSeason.id}
              id={nowEditSeason.id.toString()}
              strategy={verticalListSortingStrategy}
            >
              <Card>
                <ScrollArea className="h-[calc(100vh-(57px+64px+216px+16px+2px))]">
                  <div className="flex flex-col gap-2 p-4">
                    {nowEditSeason.episodes.map((episode) => (
                      <Sortable
                        key={episode.id}
                        id={episode.id}
                        className="hover:bg-zinc-100"
                      >
                        <EpisodeItem data={episode} />
                      </Sortable>
                    ))}
                  </div>
                </ScrollArea>
              </Card>
            </SortableContext>
          </div>
          <DragOverlay>
            {activeId ? (
              isSelectSeason ? (
                <MenuSeasonItem value={nowEditSeason.title} />
              ) : (
                <EpisodeItem
                  data={nowEditSeason.episodes.find((ep) => ep.id == activeId)!}
                />
              )
            ) : null}
          </DragOverlay>
        </DndContext>
      </div>
    </div>
  );
}
