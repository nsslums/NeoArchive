import { Link } from "@remix-run/react";
import {
  NavigationMenu,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  navigationMenuTriggerStyle,
} from "./ui/navigation-menu";
import { Separator } from "./ui/separator";
import { Avatar, AvatarFallback, AvatarImage } from "./ui/avatar";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "./ui/dropdown-menu";

const data: { title: string; href: string }[] = [
  {
    title: "Home",
    href: "/",
  },
  {
    title: "Season 1",
    href: "/season/1",
  },
  {
    title: "Play 1",
    href: "/play/1",
  },
  {
    title: "Edit mock",
    href: "/edit/mock",
  },
];

export const Header = () => {
  return (
    <header>
      <NavigationMenu>
        <NavigationMenuList>
          {data.map((value, index) => (
            <NavigationMenuItem key={index}>
              <Link to={value.href} className={navigationMenuTriggerStyle()}>
                {value.title}
              </Link>
            </NavigationMenuItem>
          ))}
        </NavigationMenuList>
        <NavigationMenuList>
          <NavigationMenuItem className="flex items-center p-2">
            <DropdownMenu>
              <DropdownMenuTrigger>
                <Avatar className="size-10">
                  <AvatarImage src="https://github.com/shadcn.png" />
                  <AvatarFallback>TEST</AvatarFallback>
                </Avatar>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuLabel>My Account</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem>Log out</DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </NavigationMenuItem>
        </NavigationMenuList>
      </NavigationMenu>
      <Separator />
    </header>
  );
};
