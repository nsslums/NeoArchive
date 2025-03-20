import { UniqueIdentifier } from "@dnd-kit/core";
import { EpisodeDetail } from "~/routes/edit.$id";
import { Card } from "../ui/card";
import { Skeleton } from "../ui/skeleton";
import { CiMenuBurger } from "react-icons/ci";

type EpisodeItemProps = {
  data: EpisodeDetail;
};

export function EpisodeItem({ data }: EpisodeItemProps) {
  return (
    <Card className="p-2 flex gap-4">
      <div className="flex justify-center items-center ml-0.5 gap-[10px]">
        <CiMenuBurger size={15} />
      <Skeleton className="w-48 aspect-video" />
      </div>
      <div>
        <p>{data.number}</p>
        <p>{data.subtitle}</p>
        <p>{}</p>
      </div>
    </Card>
  );
}
