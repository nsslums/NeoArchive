import type { MetaFunction } from "@remix-run/node";
import { Button } from "~/components/ui/button";

export const meta: MetaFunction = () => {
  return [
    { title: "IASS" },
    { name: "description", content: "Welcome to IASS!" },
  ];
};

export default function Index() {
  return (
    <div>
      <Button>Click Me</Button>
    </div>
  );
}
