import { Skeleton } from "./ui/skeleton";

type CardArgs = {
  Title: string;
  Description?: string;
};

export const AnimeCard = ({ Title, Description }: CardArgs) => {
  return (
    <div className="p-4">
      <Skeleton className="h-[170px] w-[288px]"/>
      <Skeleton className="mt-3 h-3 w-[250px]"/>
      <Skeleton className="mt-1 h-3 w-[150px]"/>
    </div>
  );
};
