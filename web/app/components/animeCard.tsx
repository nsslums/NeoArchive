import { Link } from "@remix-run/react";
import { Skeleton } from "./ui/skeleton";

type CardArgs = {
  Title: string;
  Description?: string;
  href: string;
};

export const AnimeCard = ({ Title, Description, href }: CardArgs) => {
  return (
    <div className="w-full p-1">
      <Link to={href} className="w-full h-full">
        <Skeleton className="w-full aspect-video" />
        <Skeleton className="mt-3 h-3 w-[250px]" />
        <Skeleton className="mt-1 h-3 w-[150px]" />
      </Link>
    </div>
  );
};
