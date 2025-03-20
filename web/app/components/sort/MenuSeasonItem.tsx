import { UniqueIdentifier } from "@dnd-kit/core";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from "../ui/dropdown-menu";
import { Button } from "../ui/button";
import { MoreVerticalIcon } from "lucide-react";

export function MenuSeasonItem({ value }: { value: string }) {
  return (
    <div className="w-full p-2 flex justify-between items-center gap-1">
      <span>{value}</span>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            variant="ghost"
            className="flex size-5 text-muted-foreground"
            size="icon"
          >
            <MoreVerticalIcon />
            <span className="sr-only">Open Menu</span>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" className="w-32">
          <DropdownMenuItem>Edit</DropdownMenuItem>
          <DropdownMenuSeparator />
          <DropdownMenuItem>
            <span className="text-red-800">Delete</span>
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  );
}
